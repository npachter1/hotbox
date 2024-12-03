<?php

namespace App\Models\Dispensary;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;
use App\Models\Auth\Location;

use App\Helpers\LocationScope;
use App\Helpers\FilterScopes;
use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use DB;
use Carbon\Carbon;
use Exception;
use Log;


/**
 * Class Reward.
 */
class RewardTrigger extends Eloquent
{
    
    use SoftDeletes,FilterScopes;


    /**                                 **/
	/**       CONFIG VARS               **/
	/**                                 **/
	
    protected $table = 'dispensary_reward_triggers';
    protected $guard_name = 'api';                                              // default guard is API
	public $incrementing = true;                                                // false if id is in an integer format for users
    public $primaryKey = 'id';                                                  // with non increnting we have to specify
	
	protected $fillable = ['type','point_amount','descriptor','notes','is_exclusive','is_active'];
	protected $dates = ['archived_at','created_at','updated_at','deleted_at'];
    protected $casts = ['is_exclusive'=>'boolean','is_active'=>'boolean','point_amount'=>'float'];



    /**                                 **/
	/**       RELATIONS [with]          **/
	/**                                 **/

    public function rewards(){
        return $this->hasMany(Reward::class,'trigger_id','id');
    }
    


    /**                                 **/
	/**       FILTERS (local scope)     **/
	/* inc. /Helpers/ScopeFilters trait **/

    /* scope text match */
    public function scopeOfTextFilter($query, $text){
        
        if (!$text) return $query;

        return $query->where(function ($query) use ($text) {
            $query->where('descriptor', 'like', '%'.$text.'%')
                ->orWhere('notes', 'like', '%'.$text.'%')
                ->orWhere('type', 'like', '%'.$text.'%');
        });

    }
    
    
    /* filter by frontend query orderBy filter */
    public function scopeOfActive($query){
        
        return $query->whereNull('archived_at');

    }




    /**                                 **/
	/**       ATTRIBUTION (orm)         **/
	/**                                 **/








    /**                                 **/
	/**       UTILITIES (init/calcs)    **/
	/**                                 **/

    /* get discounts filter list data */
    public static function _getFilterList($rel='all'){

        $schema = AppSchema::getSchema('reward_schema');
        return self::select('id','type','descriptor')->ofHasFilter($rel)->ofActive()->get()->map(function($item,$key)use($schema){
            return (object)[
                'id'        => $item->id,
                'name'      => (($parse = Util::_objArraySearch(data_get($schema,'form.triggers.properties.type.values',[]),'id',$item->type))!=null ? data_get($parse,'name',ucwords(preg_replace('/[^A-Za-z0-9-]/',' ',$item->type))) : ucwords(preg_replace('/[^A-Za-z0-9-]/',' ',$item->type)))
            ];
        });

    }


    /* get and dynamically inject ui schema for this models frontend vue filtering */
    public static function _getSchema(){
        
        $schema = AppSchema::getSchema('reward_schema');
        return $schema;
        
    }



    /* model boot util - generate a unique suite integer upon creation */
    protected static function boot(){
        
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->toDateTimeString();
        });
        
    }

}