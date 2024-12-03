<?php

namespace App\Models;

use Eloquent;

use Carbon\Carbon;


/**
 * Class User.
 */
class AppSchema extends Eloquent
{
    

    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'auth_schema';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = false;                                               // false cause code is varchar
    public $primaryKey = 'code';                                                // with non increnting we have to specify
	
	protected $fillable = ['type'];
	protected $dates = ['created_at','updated_at','deleted_at'];
    protected $casts = ['content'=>'object'];


    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get schema data by code */
    public static function getSchema($code=null){
        
        //if(($schema = self::find($code)) == null) return null;
        if(($schema = json_decode(file_get_contents(database_path('schemas/'.$code.'.json')))) == null) return null; // get schema from source code database/schemas/[code].json

        return $schema->content;
        
    }
    
    
    /* set data by code */
    public static function setSchema($code=null,$content=null){
        
        //if(($schema = self::find($code)) == null) return false;
        if(($schema = json_decode(file_get_contents(database_path('schemas/'.$code.'.json')))) == null) return false; // get schema from source code database/schemas/[code].json
        elseif(!$content) return false;
        
        $schema->content = $content;

        $fp = fopen(database_path('schemas/'.$code.'.json'), 'w');
        fwrite($fp, json_encode($schema));
        fclose($fp);
        
        
        $schema->save();
        return true;
        
    }
    
    
    public static function getSchemaDate($code=null){
        
        //if(($schema = self::find($code)) == null) return null;
        if(($schema = json_decode(file_get_contents(database_path('schemas/'.$code.'.json')))) == null) return null; // get schema from source code database/schemas/[code].json
        
        return $schema->created_at;
        
    }
    
    
    
    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
        static::saved(function($model) {
            
            
            
            
            $backup = AppSchema::get()->toJson();                               // store a json file of all the AppSchemas for backup and reload.
            \Storage::put('vault/app_schema_back',$backup);
            file_put_contents(database_path('data').'/app_schema_seed.json',$backup);
            
        });
        
    }
    

}
