<?php

namespace App\Models\Auth;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Client;

use App\Models\AppSchema;
use App\Models\Dispensary\Discount;
use App\Models\Dispensary\Customer;

use App\Helpers\LocationScope;
use App\Helpers\Generator;
use App\Helpers\Util;
use App\Helpers\FilterScopes;

use Auth;
use Carbon\Carbon;


/**
 * Class User.
 */
class Location extends Eloquent
{

    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/

    protected $table = 'auth_locations';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = false;                                               // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify

	protected $fillable = ['name','thumb','addressbook_id','settings','migration_settings','archived_at','licensenum','financial_settings'];
	protected $dates = ['activated_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['settings'=>'object','is_demo'=>'boolean', 'migration_settings'=>'object','financial_settings' => 'object'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/*  users that can access this location (pivot) */
	public function users(){
		return $this->belongsToMany(User::class,'auth_location_user','auth_location_id','user_id');
	}


	/* get prime user of this location */
	public function address(){
		return $this->hasOne(Addressbook::class,'id','addressbook_id');
	}

    public function discounts(){
        return $this->belongsToMany(Discount::class,'auth_location_dispensary_discount','location_id','discount_id');
    }


	public function customers(){
		return $this->hasMany(Customer::class,'location_id','id');
	}



    /**                                 **/
	/**       FILTERS (local scope)     **/
	/**                                 **/

    /* get users with access to this location */
    public function scopeOfUser($query, $userId){

        if($userId)
            $query->where('location_id',$userId);

        return $query;
    }


    /* scope text match */
    public function scopeOfTextFilter($query, $text){

        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%');
        });

    }







    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/






    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setSettingsAttribute($data){

        if(($schema = self::_getSchema()) == null) $this->attributes['settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'settings')) == null) $this->attributes['settings'] = json_encode($data);
        else $this->attributes['settings'] = json_encode($store);

    }

    public function setMigrationSettingsAttribute($data){

        if(($schema = self::_getSchema()) == null) $this->attributes['migration_settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'migration_settings')) == null) $this->attributes['migration_settings'] = json_encode($data);
        else $this->attributes['migration_settings'] = json_encode($store);

    }

    public function setFinancialSettingsAttribute($data){

        if(($schema = self::_getSchema()) == null) $this->attributes['financial_settings'] = json_encode($data);
        elseif(($store = Util::setObjectFromSchema($data,$schema,'financial_settings')) == null) $this->attributes['financial_settings'] = json_encode($data);
        else $this->attributes['financial_settings'] = json_encode($store);

    }



    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    public function _setSetting($code,$val){

        $set = $this->settings;
        data_set($set,$code,$val,true);
        $this->settings = $set;

        return $this;

    }





    /* get ui schema for this model */
    public static function _getSchema(){

        $schema = AppSchema::getSchema('location_schema');
        $email = (Auth::guard('api')->user() ? Auth::guard('api')->user()->email : null);

        data_set($schema,'model.settings.communication_email', $email, false);  // default user email for this setting in schema
        data_set($schema,'form.settings.sections.communication.properties.communication_timezone.values',Util::getTimezones(), true); // get latest timezone list from php
        data_set($schema,'form.addressbook_id.values',Addressbook::whereIn('type',['location','vendor'])->select('id','name')->get(),true);
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query(),$schema,'status'),true);

        return $schema;

    }


    /* model boot util - generate a unique location integer upon creation */
    protected static function boot(){

        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (isset($model->{$model->getKeyName()}) ? $model->{$model->getKeyName()} : Generator::uniqueLocation());
            $model->created_at = Carbon::now()->toDateTimeString();
        });
    }


}
