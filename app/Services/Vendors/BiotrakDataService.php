<?php

namespace App\Services\Vendors;

use App\Models\Auth\Servicelog;
use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Jobs\BioTrackMigration;
use App\Jobs\GrowMigration;

use Illuminate\Support\Facades\DB;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Log;



class BiotrakDataService extends DB
{
    
    protected $user;
    protected $connection;
    protected $backfillDate;


    public function __construct() {
        
        if(($this->user = Auth::guard('api')->user())==null) abort(403,'Authentication is required to use this service');
        
        $this->connection = 'migrate_'.$this->user->location_id;
        $this->backfillDate = Carbon::parse(data_get($this->user->location,'migration_settings.backfill','2019-01-01 00:00:01'));
        
        config(['database.connections.'.$this->connection => [
            'driver'    => 'pgsql', // TODO make this a setting selection
            'host'      => data_get($this->user->location,'migration_settings.btdb_host',null),
            'port'      => data_get($this->user->location,'migration_settings.btdb_port',null),
            'database'  => data_get($this->user->location,'migration_settings.btdb_dbname',null),
            'username'  => data_get($this->user->location,'migration_settings.btdb_username',null),
            'password'  => data_get($this->user->location,'migration_settings.btdb_password',null),
            'charset'   => 'utf8',
            'prefix'    => '',
            'schema'    => data_get($this->user->location,'migration_settings.btdb_schema',null),
            'sslmode'   => 'prefer'        
        ]]); 
        DB::purge($this->connection);                                           // uncache the config database connection
    }


    /* load the connection and set the table for the db loaded */
    public function from($table){
        
        return self::connection($this->connection)->table($table);              // As extended from the DB facade, here we establish the db connection and table abstractly
        
    }


    /* get the location for this migration after initial credentials are prompted - also used to test link */
    public function getBioTrakLocation() {
        
        $hb_licensenum = $this->user->location->licensenum;
        $bt_locationtypes = [ 'grow' => 1, 'dispensary' => 2 ];
        $is_medical = $this->user->location->settings->is_medical;

        $bt_locations = $this->from('locations')->get();
        
        /* filter the locations that are appolicable, and return the name/id in the passed collection */
        $found = $bt_locations->filter(function($location,$key)use($hb_licensenum,$bt_locationtypes){
            return (($location->licensenum == $hb_licensenum || $location->growlicensenum == $hb_licensenum) && $location->deleted == 0 && $location->locationtype == data_get($bt_locationtypes,$this->user->location->type,0) ? true : false);
        })->map(function($location,$key){
            return (object)[
                'name'      => $location->name, 
                'id'        => $location->id
            ];
        })->values()->toArray();

        $other_locations = [];
        if ($is_medical) {
            if ($this->user->location->type == 'grow') {
                $other_locations = $bt_locations->filter(function($location, $key) use($hb_licensenum, $bt_locationtypes) {
                    return (($location->medical && $location->deleted == 0 && $location->locationtype == 2) ? true : false);
                })->map(function($location, $key) {
                    return (object)[
                        'name' => $location->name,
                        'id'   => $location->licensenum
                    ];
                })->values()->toArray();
            }
            else if ($this->user->location->type == 'dispensary') {
                $other_locations = $bt_locations->filter(function($location, $key) use($hb_licensenum, $bt_locationtypes) {
                    return (($location->medical && $location->deleted == 0 && $location->locationtype == 1) ? true : false);
                })->map(function($location, $key) {
                    return (object)[
                        'name' => $location->name,
                        'id'   => $location->growlicensenum
                    ];
                })->values()->toArray();
            }
        }
        
        
        return [
            'db_connected'      => ($bt_locations->count()>0 ? true : false),
            'found_locations'   => $found, 
            'licensenum'        => $hb_licensenum,
            'other_locations'   => $other_locations
        ];
        
    }
    
    
    /* begin a Migration Job */
    public function startMigration($data,$typ='btdb',$user) {
        
        if(($location = Location::find(data_get($data,'location.id',null))) == null) abort(422,'Could not load current location from migration form - try again?');
        elseif(($match_location = data_get($data,'location.migration_settings.btdb_location_id',null))==null) abort(422,'No Source Location Match recorder - try again?');
        

        if($location->type == 'dispensary'){
            
            switch($typ){
                case 'btdb':
                    $location->status = 'migrating';
                    $location->migration_settings = data_get($data,'location.migration_settings',null);
                    $location->associated_licensenums = json_encode(data_get($data, 'location.migration_settings.btdb_associated_licensenums', null));
                    $location->save();                                          // persist any new migration settings from step 2, including the matching locations for biotrak db                    
                    
                    BioTrackMigration::dispatch($location, $this->user);        // We are sending the current location and user data to be serialized.
                break;
                default: //
            }
            
        }elseif($location->type == 'grow') {
            
            GrowMigration::dispatch($location, $this->user);
            
        }


        return [
            'status'    => 200,
            'message'   => 'Migration has begun!  You will be emailed once complete.',
        ];

    }

    
}