<?php

namespace App\Services\Metrc;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Psr7;
use GuzzleHttp\Middleware;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Promise;

/* check traits for all metrc services */
//use App\Services\Metrc\Traits\MetrcEmployeeApi;
use App\Services\Metrc\Traits\MetrcFacilityApi;
use App\Services\Metrc\Traits\MetrcHarvestApi;
use App\Services\Metrc\Traits\MetrcItemApi;
//use App\Services\Metrc\Traits\MetrcLabtestApi;
use App\Services\Metrc\Traits\MetrcPackageApi;
use App\Services\Metrc\Traits\MetrcPatientApi;
use App\Services\Metrc\Traits\MetrcPlantApi;
use App\Services\Metrc\Traits\MetrcPlantBatchesApi;
use App\Services\Metrc\Traits\MetrcRoomApi;
//use App\Services\Metrc\Traits\MetrcSaleApi;
use App\Services\Metrc\Traits\MetrcStrainApi;
//use App\Services\Metrc\Traits\MetrcToDbTransformer;
use App\Services\Metrc\Traits\MetrcTransferApi;
//use App\Services\Metrc\Traits\MetrcUnitOfMeasureApi;

use App\Models\Auth\Servicelog;
use App\Models\Auth\User;
use App\Models\Auth\Location;

use App\Services\BaseService;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Log;



class MetrcRequestService extends BaseService
{

    use MetrcItemApi;
    use MetrcRoomApi;
    use MetrcStrainApi;
    use MetrcPlantBatchesApi;
    use MetrcPlantApi;
    use MetrcHarvestApi;
    use MetrcFacilityApi;
    use MetrcPackageApi;
    use MetrcPatientApi;
    //use EmployeeApi,
    //use MetrcLabtestApi,
    //use MetrcSaleApi,
    //use MetrcToDbTransformer,
    use MetrcTransferApi;
    //MetrcUnitOfMeasureApi;


    const METRC_TIMEZONE = 'UTC';
    public const METRC_MAX_REQUESTS_PER_MINUTE = 30;

    public $metrcTransactionId;

    protected $token;
    protected $base_url;
    protected $uri;
    protected $api_token;
    protected $duration;
    protected $state;


    public function __construct() {}



    // Request methods:
    public function get($user, $url, $parameters=[], $addLicenseNumber=true){
        return $this->execute($user, $url, 'GET', $parameters, $addLicenseNumber);
    }

    public function post($user, $url, $parameters=[], $addLicenseNumber=true){
        return $this->execute($user, $url, 'POST', $parameters, $addLicenseNumber);
    }

    public function put($user, $url, $parameters=[], $addLicenseNumber=true){
        return $this->execute($user, $url, 'PUT', $parameters, $addLicenseNumber);
    }

    public function patch($user, $url, $parameters=[], $addLicenseNumber=true){
        return $this->execute($user, $url, 'PATCH', $parameters, $addLicenseNumber);
    }

    public function delete($user, $url, $addLicenseNumber=true){
        return $this->execute($user, $url, 'DELETE', [], $addLicenseNumber);
    }


    /**
     * @param $user User is required for logging as well as determining license number.
     * @param $url Path of the API endpoint
     * @param string $method Either GET, POST, PUT, PATCH, or DELETE
     * @param array $parameters A simple array of arrays containing data
     * @param bool $addLicenseNumber If true, automatically add the licenseNumber parameter to the querystring
     * @return RestClient
     */
    public function execute($user,$url,$method='GET',$parameters=[],$addLicenseNumber=true){

        if(!($user instanceof User)) abort(422,'Couldnt access user data - Aborting');
        elseif(($location = $user->location) == null) abort(422,'Couldnt access users logged in as location data - Aborting');
        elseif($location->is_demo && $method != 'GET') abort(422,'Your Location '.$location->name.' is in demo mode - metrc editing is being skipped.');
        elseif(($license = data_get($location, 'licensenum',null)) == null) abort(422,'No User License Provided - Aborting');
        elseif(($this->uri = data_get($location,'settings.metrc_uri',null)) == null) abort(422,'Your location does not have a Metrc URI provided - Please check your settings.');
        elseif(($this->api_token = data_get($location,'settings.metrc_token',null)) == null) abort(422,'Your location does not have a Metrc Token provided - Please check your settings.');

        $this->metrcTransactionId = null;
        $store = $this; // for the on_stats method to use

        while (self::currentRequestsPerMinute() > self::METRC_MAX_REQUESTS_PER_MINUTE)
            sleep(10); // all queued jobs are on a "metrc" queu with Horizon Redis - so we can throttle the jobs as well as this hack


        /* Instantiate Rest Client */
        $client = new Client([
                'base_uri'          => $this->uri,
                'timeout'           => 180,                                     // 2 min timeout
                'debug'             => false,
                'allow_redirects'   => false,
                'on_stats'          => function (TransferStats $stats) use (&$store) {
                    $store->base_url = $stats->getEffectiveUri();
                    $store->duration = $stats->getTransferTime();
                 }
        ]);

        if ($addLicenseNumber) $url .= strpos($url, '?') ? '&'.'licenseNumber='.$license : '?'.'licenseNumber='.$license;

        $transaction = new Servicelog();
        $transaction->type = 'METRC';
        $transaction->method = strtoupper($method);
        $transaction->base_url = $this->uri;
        $transaction->processed_url = $this->base_url;
        $transaction->path = $url;

        if ($parameters && count($parameters)){
            $url .= (strpos($url, '?') ? '&' : '?').http_build_query($parameters);
            $transaction->parameters = json_encode($parameters);
            $parameters = [];
        }

        $transaction->location_id = $location->id;
        $transaction->created_by = $user->id;




        try {
            $result = $client->request($method,$url,[
                'headers' => [
                        'Authorization'  => 'Basic '.$this->api_token,
                        'Content-Type'   => 'application/json',
                        'Accept'         => 'application/json'
                    ],
                'json' => $parameters
            ]);

            $data = (string)$result->getBody();

            $transaction->http_code = $result->getStatusCode();
            $transaction->response = $data;
            //$transaction->error = $result->error;
            $transaction->total_time = $this->duration;

            $transaction->save();
            $this->metrcTransactionId = $transaction->id;

            return (object)['http_code'=>$transaction->http_code,'data'=>json_decode($data)];
        }catch (Exception $e) {

            $transaction->exception = json_encode($e);
            $transaction->save();
            $this->metrcTransactionId = $transaction->id;

            throw $e;
        }
    }



    public function getMetrcDateTimeString(Carbon $carbon) {
        return $carbon->toIso8601String();
    }

    public function getMetrcDateString(Carbon $carbon) {
        return $carbon->format('Y-m-d');
    }

    public function getCarbonDateFromMetrcString(string $metrcDateString) {
        return Carbon::createFromFormat(DATE_ISO8601, $metrcDateString, self::METRC_TIMEZONE);
    }

    public function getMetrcCarbonDateNow() {
        return Carbon::now(self::METRC_TIMEZONE);
    }

    public static function currentRequestsPerMinute() {
        return Servicelog::where('created_at', '>=', Carbon::now('UTC')->subMinutes(1)->toDateTimeString())->count();
    }


}
