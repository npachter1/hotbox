<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

use App\Models\AppSchema;
use App\Models\Dispensary\Drawer;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Helpers\FilterScopes;
use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use Carbon\Carbon;
use DB;


/**
 * Class User.
 */
class User extends Authenticatable
{
    
    use HasApiTokens,HasRoles,FilterScopes,Notifiable,HasPushSubscriptions;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'users';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = false;                                               // false if id is in uuid format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['name','status','avatar','settings'];
	protected $hidden = ['password', 'remember_token'];
	protected $dates = ['trial_ends_at','archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['settings'=>'object'];

    //User::withoutGlobalScope(LocationScope::class)->get();



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

	/**  user can access locations from pivot table */
	public function locations(){
		return $this->belongsToMany(Location::class,'auth_location_user','user_id','auth_location_id');
	}

    public function locationsAssigned(){
        return $this->belongsToMany(Location::class,'auth_location_user','user_id','auth_location_id')->select('id','name','type','is_demo');
    }

	/**  user location */
	public function location(){
		return $this->hasOne(Location::class,'id','location_id');
	}

    public function drawers(){
        return $this->hasMany(Drawer::class,'user_id','id');
    }

    public function openDrawers() {
        return $this->hasMany(Drawer::class)
            ->whereNull('closed_at')
            ->orderBy('created_at', 'ASC');
    }


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/
    
    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('name', 'like', '%'.$text.'%')
                ->orWhere('email', 'like', '%'.$text.'%');
        });

    }
    
    /* get by logged in users location */
    public function scopeOfLocation($query){
        
        if(($user = Auth::guard('api')->user()) == null) return $query;         // if we havea logge din user, filter by location
        return $query->where('location_id',$user->location_id);
        
    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }
    



    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/











    /* Cast properties and/or assume defaults of this settings array by schema.form. */
    public function setSettingsAttribute($data){
        
        if(($schema = self::_getSchema()) == null) $this->attributes['settings'] = json_encode($data);
        else $this->attributes['settings'] = json_encode(Util::setObjectFromSchema($data,$schema,'settings'));

    }



    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/











    /* get ui schema for this model */
    public static function _getSchema(){
        
        $user = Auth::guard('api')->user();
        $schema = AppSchema::getSchema('user_schema');


        data_set($schema,'form.location_id.values',Location::query()->select('id','name')->where('status','activated')->get(),true); // current locations for default location

        data_set($schema,'filters.type.values',Util::getActiveFilterList(self::query(),$schema,'type'),true);
        data_set($schema,'filters.status.values',Util::getActiveFilterList(self::query(),$schema,'status'),true);

        data_set($schema,'filters.location_id.focus',($user ? $user->location->id : null)); // focus the location id filter tabs to the users current location id
        data_set($schema,'filters.location_id.values',Util::getActiveFilterList(self::query()->whereIn('location_id',$user->locationsAssigned()->where('type',$user->location->type)->pluck('id')->toArray()),$schema,'location_id'),true);

        data_set($schema,'form.role_ids.values',Role::select('id','name')->get()->map(function ($item, $key) { // add all roles, and weather or not curent user can edit
            if($item->name=='super-admin'){ $item->isDisabled = true; $item->isHidden = true; }
            elseif(!Auth::guard('api')->user()->can('Staff Update') || $item->name=='super-admin') $item->isDisabled = true;
            else $item->isDisabled = false;

            $item->description = ($item->name!='super-admin' ? 'Role can: '.join(', ',$item->permissions->map(function($perm,$key){ return $perm->name; })->toArray()) : 'Do Everything as Anyone.');
            unset($item->permissions);                                          // from describing permissions, remove relation from array data
            
            return $item;
        }));


        return $schema;
        
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.User.'.$this->id;
    }
    

    /* model boot util */
    protected static function boot(){
        
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (isset($model->{$model->getKeyName()}) ? $model->{$model->getKeyName()} : Generator::uuid4());
        });
    }
    

}
