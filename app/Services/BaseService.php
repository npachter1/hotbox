<?php
namespace App\Services;

use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Models\AppSchema;
use App\Models\Auth\User;

use Exception;
use Image;
use Excel;
use Storage;
use PDF;
use Illuminate\Support\Str;

/**
 * Class AppService
 * @package App\Services
 */
class BaseService
{
   
    // DispatchesJobs: This trait provides several methods allowing you to
    // conveniently push jobs onto the queue, such as the dispatch method.
    use DispatchesJobs;


    /* get model schemas */
    public function loadSchemas($data,$module,$user){

        if(!$user instanceof User) abort(403,'Whoops - user data not provided - aborting.');
        elseif(!is_array($data)) abort(422,'Whoops - No incorredt caller data type - aborting.');

        $schemas = [];

        foreach($data as $mod){
            switch($module){
                case 'auth':
                    $class = 'App\\Models\\'.ucwords($module).'\\'.ucwords($mod);
                    if($mod=='locations') $schemas['locationsSchema'] = \App\Models\Auth\Location::_getSchema();
                    elseif(class_exists($class))
                        $schemas[strtolower($mod).'Schema'] = $class::_getSchema();
                    break;
                case 'superadmin':
                    if($mod=='servicelog') $schemas['servicelogSchema'] = \App\Models\Auth\Servicelog::_getSchema();
                    break;
                default: 
                    $class = 'App\\Models\\'.ucwords($module).'\\'.ucwords($mod);
                    if(class_exists($class))
                        $schemas[strtolower($mod).'Schema'] = $class::_getSchema();
            }
        }      


        return $schemas;
        
    }



    /* upload image/file */
    public function upload($data,$model,$typ,$locationId){
        
        if(!$data) throw new Exception('Whoops - We couldnt fetch the upload data - aborting.');
        //s3 valid filename chars: 0-9,a-z,A-Z,!-_.*'()
        $filename = preg_replace("([^\w!\-'\.])", '', str_replace(' ','-',$data->getClientOriginalName()));
        if ($filename==='' || substr($filename, 0, 1)==='.') $filename='file'.($data->getClientOriginalExtension() ? '.'.$data->getClientOriginalExtension() : '');
        $path = 'media/'.$locationId.'/'.strtolower($model).'/'.Str::uuid()->toString().'/';
        Storage::disk('s3')->putFileAs($path,$data,$filename);
        return Storage::disk('s3')->url($path.$filename);
    }
    

    /* process, store, and download an export (ie: grid csv, profile pdf) */
    public function export($data,$path,$name,$typ,$options=[]){
        
        if(!$data || !$path || !$name || !$typ) throw new Exception('Whoops - Incomplete information sent to exporter - aborting.');
        
        
        /* Store, and Return Export file */
        switch($typ){
            case 'pdf':
                Storage::disk('s3')->put($path.$name,$data);                    // store the raw data on s3 bucket
                return Storage::disk('s3')->download($path.$name, $name,['file_name'=>$name]); // return a download of what was stored
                break;
            case 'csv':
                Excel::store($data,$path.$name, 's3', null,['visibility'=>'private']); // do an excel store to s3
                return Storage::disk('s3')->download($path.$name,$name,['file_name'=>$name]);
                break;
            default: throw new Exception('Whoops - incorrect download/export type provided - aborting.');
        }
    
    }

}
