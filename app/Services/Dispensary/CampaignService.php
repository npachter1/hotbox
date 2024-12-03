<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\CampaignCollectionExport;

use App\Models\AppSchema;
use App\Models\Dispensary\Campaign;
use App\Models\Dispensary\CampaignLog;
use App\Models\Dispensary\CustomerQueue;
use App\Models\Dispensary\Group;
use App\Models\Dispensary\Discount;
use App\Models\Dispensary\Sale;

use App\Services\Dispensary\GroupService;
use App\Services\Dispensary\DiscountService;

use Twilio\Rest\Client;
use App\Helpers\Util;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class CampaignService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Campaign::query()
            ->with('group','discount','sales')
            ->withCount('customerLog')
            ->ofListFilters($filters)
            ->ofTextFilter($search);
            
        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Campaign record */
    public function show(array $data,$id){
        
        return Campaign::query()
            ->with('group','discount','customerLog.customerAbbv.address')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Campaign record  */
    public function create($data,$user=null){
        
        /* new record */
        $campaign = new Campaign();

        $campaign->fill($data);
        $campaign->status = data_get($data,'status','pending');
        
        $campaign->scheduled_at = Carbon::parse(data_get($data,'scheduled_at',Carbon::now()->toDateTimeString()))->toDateTimeString(); // if no schedule was set, then set it to now!
        
        
        $campaign->save();
        $this->_syncLog($campaign);                                             // sync the customer notification log
        
        if($campaign->status=='working') 
            $this->runCampaign(Campaign::with('customerLog.customer.address')->find($campaign->id),$user,$campaign->type);
        
        
        return $campaign;
        
    }


    /* update Campaign */
    public function update($data,$id,$user=null){
        
        if(($campaign = Campaign::find($id)) == null) abort(422,'Campaign Resource Not Found');
        elseif($campaign->notified_count>=1) abort(409,'Cannot Update Campaign, at least 1 customer has already been notified.  Hold or archive instead.');


        $campaign->fill($data);
        $campaign->status = data_get($data,'status','pending');
        
        $campaign->scheduled_at = Carbon::parse(data_get($data,'scheduled_at',Carbon::now()->toDateTimeString()))->toDateTimeString(); // if no schedule was set, then set it to now!
        if($campaign->scheduled_at->timestamp<=Carbon::now()->timestamp && $campaign->status=='pending') $campaign->status = 'working';
        
        $campaign->save();
        $this->_syncLog($campaign);                                             // sync the customer notification log

        
        if($campaign->status=='working') 
            $this->runCampaign(Campaign::with('customerLog.customer.address')->find($campaign->id),$user,$campaign->type);
            
            
        return $campaign;
        
    }
    

    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Campaign_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new CampaignCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* Removal service */
    public function archive($data,$id,$user){
        
        if(($record = Campaign::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }
    
    
    /* 4.1.20 auto-generate a series of pos by vendorlist.items */
    public function generate($data,$user){
        
        if(($group = data_get($data,'group',null))==null) abort(422,'there was no customer group data - aborting.');
        elseif(($discount = data_get($data,'discount',null))==null) abort(422,'there was no discount rule association data - aborting.');
        elseif(!is_array(($items = data_get($data,'items',[])))) abort(422,'the associated products selected is malformed - please pass as an list/array');
        
        $groupService = new GroupService;
        $discountService = new DiscountService;
        $groupSchema = AppSchema::getSchema('group_schema');
        $discountSchema = AppSchema::getSchema('discount_schema');
        
        
        /* establish new campaign */
        $camp = new Campaign();
        $camp->campaign_code = data_get($data,'discount.discount_code','CMP-'.date('mdY',time()).rand(444,44444));
        $camp->name = data_get($data,'name','AutoGen Campaign '.date('mdY',time()).' '.rand(444,44444));
        $camp->type = 'sms'; //todo allow more types
        $camp->status = 'held';
        $camp->subject = 'Hey {{name}}';
        $camp->message = 'Come over to {{location}} for a great deal!';
        $camp->scheduled_at = Carbon::now()->toDateTimeString();                // since campaign is held, this date would be before whenever user activates it upon editing next steps..

        
        /* establish new group, or use passed id */
        if(($gid = data_get($group,'id',null))=='NEW'){
            
            $cgroup = new Group();
            $cgroup->name = data_get($group,'name','Selected Products Group '.date('mdY',time()));
            $cgroup->type = 'auto';
            $cgroup->last_synced_at = Carbon::now()->toDateTimeString();
            
            $filters = data_get($groupSchema,'model.filters',(object)[]);
            data_set($filters,'products',$items,true);
            data_set($filters,'locations',[$user->location_id],true);
            $cgroup->filters = $filters;
            

            $cgroup->save();
            $cgroup->customers()->sync($groupService->filterCustomers((array)$cgroup->filters,$user)->pluck('id')->toArray()); // sync customers based on these generated filters (ie: product)
            
            $camp->group_id = $cgroup->id;

        }else $camp->group_id = $gid;
        
        /* establish new discount, or use passed id */
        if(($did = data_get($discount,'id',null))=='NEW'){
            
            $disc = new Discount;
            $disc->name = data_get($discount,'name','Selected Products Rule '.date('mdY',time()));
            $disc->descriptor = $disc->name;
            $disc->type = data_get($discountSchema,'model.type','general');
            $disc->discount_code = data_get($discount,'discount_code',null);
            $disc->discount_type = data_get($discount,'discount_type','pct');
            $disc->discount_amount = data_get($discount,'discount_amount',1);
            $disc->distribution_type = data_get($discountSchema,'model.distribution_type','proportionate');
            $disc->settings = data_get($discountSchema,'model.settings',null);
            $disc->rank = 99;
            $disc->is_exclusive = false;
            $disc->max_per_customer = 10000;
            $disc->is_active = true;
            
            
            $disc->save();

            $disc->locations()->sync([$user->location_id]);            
            if(data_get($discount,'require_items',true)===true)
                $disc->products()->sync($items);                                // sync product_ids selected

            $camp->discount_id = $disc->id;
            $camp->message = 'Use Discount Code {{code}} for '.$disc->name.' at {{location}}!'; // update message if we have a discount present

        }elseif($did){
            
            $disc = Discount::where('id',$did)->first();
            $camp->discount_id = $disc->id;
            $camp->message = 'Use Discount Code {{code}} for '.data_get($disc,'name','Misc').' at {{location}}!'; // update message if we have a discount present
            
        }else $camp->discount_id = null;
        

        $camp->save();
        $this->_syncLog($camp);                                                 // sync the customer notification log for new campaign

        return Campaign::find($camp->id);
        
    }
    
    
    
    /* requeue customer log in campaign  */
    public function requeue($data,$id){
        
        if(($campaignLog = CampaignLog::find($id)) == null) abort(422,'Campaign Log Resource Not Found');

        $campaignLog->notified_at = null;
        $campaignLog->visited_at = null;
        $campaignLog->purchased_at = null;
        $campaignLog->code_used_at = null;
        $campaignLog->response_error = null;
        
        $campaignLog->save();
        return $campaignLog;
        
    }

    /* send a test sms message for a campaign */
    public function testSMS(array $data,$user) {
        
        $phone = Util::parsePhone(data_get($data,'test_number',null));
        $smsMessage = $this->_parseMessage(data_get($data,'subject','').' - '.data_get($data,'message',''));
        
        $client = new Client(config('services.twilio.sid'),config('services.twilio.token'));
        return $client->messages->create($phone,[
                'messagingServiceSid'   => config('services.twilio.number'),
                'body'                  => $smsMessage
            ]
        );
            
    }
    


    /* run a campaign */
    public function runCampaign($camp,$user,$typ='sms'){
    
        if($typ!='sms') return 0;
        elseif(1==2) return 0; // TODO more conditionals
    
        $count  = 0;
        $max = 100;                                                             //100 texts per call to this service
        $total = CampaignLog::where('campaign_id',$camp->id)->count();
        $limit = (data_get($user->location,'settings.campaign_sms_limit',200) - CampaignLog::whereNotNull('notified_at')->count());
    
        foreach($camp->customerLog->where('notified_at',null) as $cust){
                if($count > $max) continue;
                elseif($count > $limit){
                    $camp->delivery_status = 'You have used '.$limit.' of your '.data_get($this->user->location,'settings.campaign_sms_limit',200).' sms limit - '.Util::localDate('m/d/y g:ia');
                    $camp->status = (CampaignLog::where('campaign_id',$camp->id)->whereNull('notified_at')->count() ? 'working' : 'completed');
                    $camp->save();
                }elseif(($res = $this->_runSMS($cust,$camp))!=null){
                    $cust->notified_at = Carbon::now()->toDateTimeString();
                    $cust->save();
                    $count++;
                }
        }
                    
        $camp->delivery_status = 'Delivered '.$count.' of '.$total.' messages on '.Util::localDate('m/d/y g:ia');
        $camp->status = (CampaignLog::where('campaign_id',$camp->id)->whereNull('notified_at')->count() ? 'working' : 'completed');
        $camp->save();
        
        
        return $count;
        
    }
    


    /**                                             **/
	/**            Campaign Service Actions         **/
	/**                                             **/
	
    /* run through a campaign */
    public function _runSMS($customer,$campaign) {
        
        $phone = Util::parsePhone($campaign->sms_test_number ?: data_get($customer->customer->address,'phone',null));
        $smsMessage = $this->_parseMessage($campaign->subject.' - '.$campaign->message,$campaign,$customer->customer);
        
        $client = new Client(config('services.twilio.sid'),config('services.twilio.token'));
        return $client->messages->create($phone,[
                'messagingServiceSid'   => config('services.twilio.number'),
                'body'                  => $smsMessage
            ]
        );
            
    }
    
	
    /* sync custoemr log */
    public function _syncLog($camp){
        
        if(!$camp) return false;
        elseif(!$camp->group) return false;
        
        $count = 0;
        
        
        switch($camp->type){                                                    // specify relation to pull from group->customers* relation model
            case 'sms':
                $rel = 'customersSms';
                break;
            case 'email':
                $rel = 'customersEmail';
                break;
            default: $rel = 'customersEmail';
        }
        
        CampaignLog::where('campaign_id',$camp->id)->delete();                  // remove any existing customer notifiction logs cause we just updated the campaign.
        
        
        foreach($camp->group->$rel->chunk(20) as $custs){
            foreach($custs as $cust){
                $cl = new CampaignLog();
                $cl->created_at = Carbon::now()->toDateTimeString();
                $cl->campaign_id = $camp->id;
                $cl->customer_id = $cust->id;
                $cl->scheduled_at = $camp->scheduled_at;
                
                $cl->save();
                $count++;
            }
        }
        
        
        return $count;
        
    }
    
    
    /* log a pos visit */
    public function _logPosVisit($model){
        
        if(!$model) return false;

        $updated = 0;
        
       
        /* foreach sale triggger, transact a reward base don its settings */
        foreach(CampaignLog::with('campaign')->where('customer_id',$model->customer_id)->whereNotNull('notified_at')->whereNull('purchased_at')->get() as $clog){
            
            if($model instanceof CustomerQueue) $clog->visited_at = Carbon::now()->toDateTimeString();
            else{
            
                switch($model->status){
                    case 'settled':
                        $clog->purchased_at = Carbon::now()->toDateTimeString();
                        if($model->discount_code==$clog->campaign->campaign_code) $clog->code_used_at = Carbon::now()->toDateTimeString();
                        break;
                    case 'voided':
                    case 'refuneded':
                        // HINT reverse purchase/code used timestamp here..!
                        break;
                    default: //skip
                }
            
            }
            
            $clog->save();                                                      // saving this log will also update the agg data on the capaign!
            $updated++;
            
        }
        
        
        return $updated;

    }
    
    
    public function _logQueVisit($model){
        
        if(!$model) return false;
        $updated = 0;
        
        foreach(CampaignLog::with('campaign')->where('customer_id',$model->customer_id)->whereNotNull('notified_at')->get() as $clog){
            $clog->visited_at = Carbon::now()->toDateTimeString();
            $clog->save();                                                      // saving this log will also update the agg data on the capaign!
            $updated++;
        }
        
        
        return $updated;

    }
    
    
    
    /* parse a notification from subject-message template */
    public function _parseMessage($raw,$campaign=null,$customer=null){
        
        $user = Auth::guard('api')->user();
        
        
        return str_replace(
            [
                '{{name}}',
                '{{location}}',
                '{{campaign_code}}',
                '{{code}}'
                
                
            ],
            [
                data_get($customer,'alias',data_get($customer,'first_name','Smokey')),
                data_get($user->location,'name','Our Store'),
                data_get($campaign,'campaign_code','CMP-12345'),
                data_get($campaign,'campaign_code','CMP-12345')
                
                
            ],
            $raw
        );
        
    }
    
    
	
	


}