<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\RewardCollectionExport;

use App\Models\Dispensary\Reward;
use App\Models\Dispensary\RewardTrigger;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\Customer;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use DB;
use Excel;
use Storage;


class RewardService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Reward::query()
            ->with('saleAbbv')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Reward record */
    public function show(array $data,$id){
        
        return Reward::query()
            ->with('customer','sale')
            ->where('id',$id)
            ->first();
            
    }
    
    public function triggerShow(array $data){
        
        return RewardTrigger::query()
            ->ofActive()
            ->get();                                                            // get all triggers - the form is a list to edit/add to.
            
    }
    
    
    /* create Reward record  */
    public function create($data,$user=null){
        
        /* create a new record */

        $add = new Reward();
        $add->fill($data);
        
        $reward->customer_id = data_get($data,'customer_id',null);
        $reward->type = data_get($data,'type','misc');
        
        $add->save();
        

        return $add;
        
    }


    /* update Reward */
    public function update($data,$id,$user=null){
        
        if(($upd = Reward::find($id)) == null) abort(400,'Whoops, Reward Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        

        return $upd;
        
    }

    public function triggerUpdate($data,$id,$user=null){
        
        $registry = collect([]);
        foreach(data_get($data,'triggers',[]) as $trig){                        // update same type/amount to avoid user mistaken duplication
            $trigger = RewardTrigger::where('type',data_get($trig,'type',null))->where('point_amount',data_get($trig,'point_amount'))->first() ?: new RewardTrigger;
            $trigger->fill($trig);
            $trigger->archived_at = null;                                       // in case we have an archived match in this new post.
            $trigger->save();
            $registry[] = $trigger;
        }
        
        // archive anything not registered from this post
        DB::table('dispensary_reward_triggers')->whereNotIn('id',$registry->pluck('id')->toArray())->update(['archived_at' => Carbon::now()->toDateTimeString()]);

        return [
            'status'    => 200,
            'message'   => 'Updated Trigger registry with '.$registry->count().' Triggers',
            'schema'    => Reward::_getSchema()                                 // include schema as these batch changes may update the scope of some filters.
        ];

        
    }
    
    

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Reward_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new RewardCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* Removal service */
    public function archive($data,$id,$user){
        
        if(($record = Reward::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    
    
    /**                                             **/
	/**            Sales Service Actions            **/
	/**                                             **/    
    
    /* create a reward transaction service */
    public function _transactReward($typ,$model,$user){
        
        // used by various resource save hooks (ie a sale) - transact a reward by type and users location settings
        // only returns true or false
        
        if(!$typ || !$model) return false;
        elseif(!$user->location) return false;
        
        $discountApplied = false;
        $redemptionApplied = false;
        
        
        /* see if we are redeeming points */
        if($typ=='sale'){
            $model->load('discounts');
            foreach($model->discounts as $discount){
                if(data_get($discount,'applied.is_active',0)==1){
                    $discountApplied = true;
                    if(($amt = data_get($discount,'settings.pointsToRedeem',0))>0){
                        $redemptionApplied = true;
                        $reward = Reward::where('sale_id',$model->id)->first() ?: new Reward();
                        $reward->customer_id = $model->customer_id;
                        $reward->trigger_id = null;
                        $reward->sale_id = $model->id;
                        $reward->descriptor = 'Redeemed '.$amt.' Points from '.$discount->descriptor.' '.$discount->discount_code;
                        $reward->points_transacted = floor($amt)*-1;                // debit amt of points from reward tally
                        $reward->save();
                    }
                }
            }
        }
        
        
        /* reward points based on triggers */
        foreach(RewardTrigger::query()->ofActive()->where('is_active',1)->get() as $trigger){
            switch($trigger->type){
                case 'sale':                                                    // process a sale trigger

                    if(!$model instanceof Sale) continue 2;                     // need saleOrder data passed
                    elseif(!in_array($model->status,['settled','refunded'])) continue 2;               // sale need be settled/refunded
                    elseif($trigger->is_exclusive==true && $discountApplied) return false; // if trigger is exclusive and a dioscount has applied - skip

                    // HINT check max per customer and max per transaction here if needed
                    
                    
                    
                    // Either update existing reward for this sale, or create new if not exist 1 per sale - even if sale is updated, then this updates the points earned
                    $reward = Reward::where('sale_id',$model->id)->first() ?: new Reward();
                    $reward->customer_id = $model->customer_id;
                    $reward->trigger_id = $trigger->id;
                    $reward->sale_id = $model->id;
                    $reward->descriptor = ($model->status=='settled' ? $trigger->descriptor : 'Return/Refunded: '.$trigger->descriptor);
                    
                    $reward->points_transacted = (floor($model->sale_price/10) * $trigger->point_amount); // sale type is for every 10, get X points
                    
                    $reward->created_at = $reward->created_at ?: $model->created_at; // NOTE lets sync the created at date with the sale for now, but may want to revisit..    
                    $reward->save();

                    break;
                case 'referral':
                    
                    if(!$model instanceof Customer) continue 2;                     // needed custoemr whom referred
                    // HINT check max referrals per customer, and scustomer who has been referred..
                    
                    $reward = new Reward();
                    $reward->customer_id = $model->id;
                    $reward->trigger_id = $trigger->id;
                    $reward->sale_id = null;
                    $reward->descriptor = $trigger->descriptor;
                    $reward->points_transacted = $trigger->point_amount;        // points per referral
                    
                    $reward->created_at = Carbon::now()->toDateTimeString();    
                    $reward->save();              
                    
                    break;
                case 'revisit':
                    
                    // TODO 
                    
                    
                    
                    
                    break;
                default: return false;
            }
        }
        

        return true;
        
    }
    


}