<?php

/**
 * Generate App-Sepecific numbers, ids, and codes
 */

namespace App\Helpers;

use App\Models\Auth\Location;
use App\Models\Dispensary\CustomerQueue;
use App\Models\Category;

use App\Models\AppSchema;

use Auth;
use DateTimeZone;
use CountryState;
use Carbon\Carbon;


class Util
{
    
    
    /* add data to array/object */
    public static function addData($add,$orig,$inSet=true){
        
        if(!$orig) return null;
        elseif(!$add) return $orig;
        
        $data = $orig;
        
        
        foreach((array)$add as $key => $val)                                    // foreach value in new data passed
            if(array_key_exists($key,(array)$orig) || $inSet==false)            // if the key is in the origional set, or the inSet flag is set to false = create new
                data_set($orig,$key,$val,true);                                 // set data
                
        
        return $data;
        
    }
    


    /* get (to/from) daterange for schema filter values */
    public static function getActiveFilterDateRange($scope='SOM'){
        
        switch($scope){
            case 'SOM':
                $from = Carbon::now()->startOfMonth()->toDateTimeString();
                break;
            default: $from = Carbon::create(config('app.start_date'))->startOfDay()->toDateTimeString();
        }
        
        
        return [
            (object)[
                'id'    => $from,
                'name'  => 'from'
                ],
            (object)[
                'id'    => Carbon::now()->endOfDay()->toDateTimeString(),
                'name'  => 'to'
                ]
        ];

    }


    /* get (to/from) daterange for schema filter values */
    public static function getActiveFilterList($q,$schema,$name){
        
        $data = [];
        
        foreach($q->selectRaw($name.',COUNT(id) as items')->whereNotNull($name)->orderBy($name)->groupBy($name)->get() as $set)
            $data[] = (object)[
                    'id'    => $set->{$name},
                    'count' => $set->items,
                    'name'  => (($parse = Util::_objArraySearch(data_get($schema,'form.'.$name.'.values',[]),'id',$set->{$name}))!=null ? data_get($parse,'name',ucwords(preg_replace('/[^A-Za-z0-9-]/',' ',$set->{$name}))) : ucwords(preg_replace('/[^A-Za-z0-9-]/',' ',$set->{$name})))
                ];
                
        
        return $data;

    }



    /* get list of timezone objects for schema select values */
    public static function getTimezones(){
        
        $timezones = DateTimeZone::listAbbreviations();
        $data = [];
        $cities = [];
        
        foreach($timezones as $key => $zones){
            foreach($zones as $id => $zone){
                if(preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $zone['timezone_id']))
                    $cities[$zone['timezone_id']][] = $key;                     // Only get timezones explicitely not part of "Others".
            }
        }
        
        foreach($cities as $key => $value)
            $cities[$key] = join( ', ', $value);
        $cities = array_unique($cities);                                        // Only keep one city (the first and also most important) for each set of possibilities. 
        ksort($cities);                                                         // Sort by area/city name.
        
        
        foreach($cities as $key => $val)                                        // organize into an object of id,name
            $data[] = (object)[
                    'id'        => $key,
                    'name'      => preg_replace('/\_|-/',' ',$key).' ('.$val.')'
                ];
                
        
        return $data;
        
    }
    
    
    /* render local date */
    public static function localDate($format='m/d/y',$date=null){
        
        $dt = ($date instanceof Carbon ? $date : Carbon::now());
        
        return (($user = Auth::guard('api')->user()) ?                          // set users suite timezone and then return formatted date string
            $dt->setTimezone(data_get($user->suite,'settings.communication_timezone','UTC'))->format($format) : 
            $dt->format($format));
        
    }
    

	/* parse a phone number for twilio */
	public static function parsePhone($num=null){
	     if($num==null) return null;
	     
            $phone = preg_replace("/[^0-9]/", "", $num);
            $length = strlen($phone);
            switch($length) {
            case 10:
             return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "+1$1$2$3", $phone);
            break;
            case 11:
            return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "+$1$2$3$4", $phone);
            break;
            default:
              return null;
            }
	}
    
    
    /* get current countries array of objects for vue select form */
    public static function getCountries(){
        
        $data = [];
        
        foreach(CountryState::getCountries() as $code => $name)
           $data[] = (object)[
               'id'     => $code,
               'name'   => $name
            ];
            
            
        return $data;
        
    }
    
    public static function getStates($ctry){
        
        $data = [];
        $ctry = $ctry ?: 'US';
        
        foreach(CountryState::getStates($ctry) as $code => $name)
           $data[] = (object)[
               'id'     => $code,
               'name'   => $name
            ];
            
            
        return $data;
        
    }


    /* get an unused Smurf Name for a customer alias/queue */
    public static function getSmurfName($caller){

        // Later these names can be abstracted out if
        // need different naming schemes.
        $names = [
            'Alchemist', 'Architect', 'Baker', 'Brainy',
            'Chef', 'Clockwork', 'Complimentary', 'Dabbler',
            'Doctor', 'Dreamy', 'Drummer', 'Editor',
            'Enamored', 'Farmer', 'Finance', 'Fisher',
            'Flying', 'Gutsy', 'Handy', 'Harmony', 'Hefty',
            'Hunter', 'Jokey', 'Karate', 'Lucky', 'Lumberjack',
            'Magician', 'Marco', 'Mime', 'Miner', 'Nanny',
            'Narrator', 'Natural', 'Navigator', 'Nurse',
            'Painter', 'Poet', 'Pretentious', 'Reflection',
            'Smooth', 'Snappy', 'Submariner', 'Sweepy', 'Tailor',
            'Tracker', 'Tuffy', 'Wild', 'Winner', 'Woolly'
        ];
        
        $used = CustomerQueue::whereNull('serviced_at')->whereNull('deactivated_at')->pluck('customer_alias')->toArray();
        
        if (count($used) > 0) {
            if (count($used) >= count($names)) {
                return false;
            }
            $names = array_diff($names, $used);
        }
        
        
        return $names[array_rand($names)];
    }
    


    /* equavelancy multipliers for metrc sync */
    public static function getMetrcEquivs($gpo) {
        
        return [
            'Buds'          => 1,
            'Flower'        => 1,
            'ShakeTrim'     => 1,
            'Concentrate'   => $gpo / 8,
            'InfusedEdible' => $gpo / 0.8,  // TODO decifer infused non edible and infused edible metrc name, cause the former has no equiv limit.
            'Edible'        => $gpo / 0.8,
            'Singleserver'  => $gpo / 0.8,
            'Plants'        => 0
        ];
    }



    /* default cateogry type imagery */
    public static function getImagery() {
        return [
            'Buds'          => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/bud.jpg',
            'Bud'           => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/bud.jpg',
            'Flower'        => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/bud.jpg',
            'ShakeTrim'     => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/shake.jpg',
            'Concentrate'   => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/concentrate.jpg',
            'InfusedEdible' => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/edibles.jpg',
            'Edible'        => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/edibles.jpg',
            'Singleserver'  => 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/edibles.jpg',
            'Plants'        => null
        ];
    }
    
    public static function guessImagery($name) {
        
        switch($name){
            case (preg_match('/bud|strain|flower/i',$name) ? true : false):
                return 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/bud.jpg';
                break;
            case (preg_match('/shake|trim/i',$name) ? true : false):
                return 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/shake.jpg';
                break;
            case (preg_match('/concentrate/i',$name) ? true : false):
                return 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/concentrate.jpg';
                break;
            case (preg_match('/edible|singleserver|single serve|infused/i',$name) ? true : false):
                return 'https://hboxcloud.s3-us-west-2.amazonaws.com/media/category/edibles.jpg';
                break;
            default: return null;
        }
        
        return null;
    }    
    
    
    
    /* set settings object from schema */
    public static function setObjectFromSchema($data,$schema,$field='settings'){
      
        if(!$data) return null;
        elseif(!$schema) return $data;
        
        $data = (array)$data;
        foreach((array)data_get($schema,'model.'.$field,[]) as $key => $val) // add defaults if empty
            if(!isset($data[$key]))
                $data[$key] = $val;
        
        foreach((array)data_get($schema,'form.'.$field.'.sections',data_get($schema,'form.'.$field,[])) as $ind => $sect){ // cast properties based on form type
            foreach((array)data_get($sect,'properties',[]) as $key => $opts){
                if(isset($data[$key])){
                    if(isset($opts->cast)) settype($data[$key],$opts->cast);
                    else{
                        switch(data_get($opts,'type',null)){
                            case 'text':
                                $data[$key] = (string)$data[$key];
                                break;
                            case 'boolean':
                                $data[$key] = (boolean)$data[$key];
                                break;
                            case 'number':
                                $data[$key] = (float)$data[$key];
                                break;
                            case 'list':
                                $data[$key] = (array)$data[$key];
                                break;
                            default: $data[$key] = $data[$key];
                        }
                    }
                }
            }
        }
            
            
        return $data;
        
    }
    
    
    
    
    /* search an array of objects - return object */
    public static function _objArraySearch($array, $index, $value){
        foreach($array as $arrayInf) {
            if($arrayInf->{$index} == $value) {
                return $arrayInf;
            }
        }
        return null;
    }
    
    

}
