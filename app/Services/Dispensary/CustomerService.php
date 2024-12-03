<?php

namespace App\Services\Dispensary;

use App\Models\Auth\Addressbook;
use App\Services\BaseService;

use App\Http\Resources\Dispensary\CustomerCollectionExport;

use App\Models\Dispensary\Customer;
use App\Models\Dispensary\CustomerQueue;
use App\Models\Dispensary\Group;
use App\Models\AppSchema;

use App\Services\Dispensary\GroupService;

use App\Services\Auth\AddressbookService;
use App\Services\Metrc\MetrcRequestService;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class CustomerService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);
        $abbv = data_get($data,'abbv',false);

        $groupIds = data_get($data,'filter.group_id',[]);


        $query = Customer::query()
            ->with('address')
            ->withCount('pendingSales','settledSales')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofAssociationFilter('groups',$groupIds);

        if(data_get($data,'archived',0)==0) $query->ofActive();                 // unless we are searhing w archived toggle on, then only get active.

        if($abbv) $query->select(Customer::$abbv_fields);

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at', 'desc');


        return $query->paginate($limit);

    }


    /* get customer Queue */
    public function searchQueue(array $data,$user){

        return CustomerQueue::query()
            ->with('customerAbbv.address')
            ->whereNull('serviced_at')
            ->whereNull('deactivated_at')
            ->orderBy('created_at')
            ->get();

    }


    /* get Customer record */
    public function show(array $data,$id){

        return Customer::query()
            ->with('address','groups','sales.payments','sales.items.inventory.product','rewards')
            ->where('id',$id)
            ->first()
            ->append('thc_limit_grams')
            ->append('thc_limit_grams_used');

    }

    /* get Customer record by email and password*/
    public function login(array $data){

        $email = data_get($data,'username','');
        $password = data_get($data,'password','');

        $customer =  Customer::query()
            ->with('address','groups','sales.payments','sales.items.inventory.product','rewards')
            ->where('online_login_email',$email)
            ->first();
            //->append('thc_limit_grams')
            //->append('thc_limit_grams_used');

        if($customer) {
            if(\Hash::check($password,$customer->password)) {
                return $customer;
            } else {
                return null;
            }
        } else {
            return null;
        }

    }

    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Customer::query()
            ->with('address')
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }


    /* create Customer record  */
    public function create($data,$user=null){

        $addrService = new AddressbookService();

        /* new record */
        $add = new Customer();

        $add->fill($data);
        $add->location_id = $user->location_id;

        if(($mmj = data_get($data,'mmj_card',null))!=null)
            $add->mmj_card = $mmj;
        if(($lic = data_get($data,'drivers_license',null))!=null)
            $add->drivers_license = $lic;

        if(($pass = data_get($data,'password',null))!=null)
            $add->password = \Hash::make($pass);


        if(($addrData = data_get($data,'address',null))!=null){
            $addr = $addrService->create($addrData);
            $add->addressbook_id = $addr->id;
        }

 /*       if ($add->type == 'patient') {
            $patient = [];
            $metrcApi = new MetrcRequestService;
            $patient[] = [
                'LicenseNumber' => $add->mmj_card,
                'LicenseEffectiveStartDate' => '',
                'LicenseEffectiveEndDate' => $add->mmj_card_expiry_date,
                'RecommendedPlants' => $add->settings->med_plant_count,
                'RecommendedSmokableQuantity' => $add->settings->med_carry_weight,
                'ActualDate' => $add->created_at
            ];
            $http_code = $metrcApi->addPatients($user, $patient);
            if ($http_code !== 200) {
                throw new \Exception('Patient Not Entered Successfully');
            }
        }           */


        $add->save();

        return $add;

    }


    /* update Customer*/
    public function update($data,$id,$user=null){
        $addrService = new AddressbookService();

        if(($upd = Customer::find($id)) == null) abort(400,'Whoops, Customer Data is temporarially disonnected - please try again later');

        $upd->fill($data);

        if(($mmj = data_get($data,'update_mmj_card',null))!=null)
            $upd->mmj_card = $mmj;
        if(($lic = data_get($data,'update_drivers_license',null))!=null)
            $upd->drivers_license = $lic;

        if(($addrData = data_get($data,'address',null))!=null){
            $upd->address->fill($addrData);
            $upd->address->save();
        }

        $upd->save();

        return $upd;

    }


    /* update batch edit data */
    public function updateBatch($data,$user=null){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no customer batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Customer::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Customer::_getSchema()                               // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* add customers in list to a new group */
    public function addToGroup($data,$user=null){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no customer batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');
        elseif(($group_id = data_get($data,'form.group',null))==null) abort(422,'Whoops, no group assigned - aborting.');

        $groupService = new GroupService();


        if($group_id=='NEW'){   // NEW GROUP

            $groupService = new GroupService();
            $group = $groupService->create([
                    'name'          => data_get($data,'form.new_name','Misc Group'),
                    'type'          => 'custom',
                    'filters'       => data_get(AppSchema::getSchema('group_schema'),'model.filters',null),
                    'customer_ids'  => $batch
                ],$user);

        }else{
            if(($group = Group::with('customers')->find($group_id))==null) abort(422,'Whoops, could not locate this group - aborting.');

            $sync = array_merge($group->customers->pluck('id')->toArray(),$batch);
            $upd = $groupService->update([                                      // merge existing customer ids with selected, and update with type = custom just in case
                    'type'          => 'custom',
                    'customer_ids'  => $sync
            ],$group->id,$user);

        }


        return [
            'status'    => 200,
            'message'   => 'We have successfully '.($group_id=='NEW' ? 'created a new Customer Group with '.count($batch).' Customers called '.data_get($group,'name','Misc Group') : 'Added '.count($batch).' Customers to '.data_get($group,'name','Misc Group')),
            'schema'    => Customer::_getSchema()                               // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* archive batch list */
    public function updateBatchArchive($data,$user=null){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no customer batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Customer::whereIn('id',$batch)->get() as $store){

            $store->archived_at = Carbon::now()->toDateTimeString();
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Customer::_getSchema()                               // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Customer_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new CustomerCollectionExport($data),$file,$name,$typ,[]);

    }


    /* removal service */
    public function archive($data,$id,$user=null){

        if(($record = Customer::with('sales')->find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        elseif($record->sales->count()>0) abort(422,'You cannot archive a customer w sales');




        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();
        //$record->delete();                                                    // A soft-delete

        return true;

    }


    /* add a customer to queue */
    public function addToQueue($data,$customerId,$user=null){

        if(($cust = Customer::find($customerId))==null) abort(422,'Whoops - We couldnt locate customers record to add to queue - Try again?');
        elseif(CustomerQueue::where('customer_id',$cust->id)->whereNull('serviced_at')->whereNull('deactivated_at')->count()>=1) abort(422,'Whoops - This customer is already on the Queue.');

        $que = new CustomerQueue;
        $que->customer_id = $cust->id;
        $que->customer_alias = $cust->alias ?: ucwords($cust->first_name).' '.ucwords(substr($cust->last_name ?: '',0,1));
        $que->save();

        return $que;

    }


    /* service a customer from queue */
    public function setServiceQueue($data,$customerId,$user=null){

        if(($cust = Customer::find($customerId))==null) abort(422,'Whoops - We couldnt locate customers record to add to queue - Try again?');

        /* service last queue entry (or create new if not yet queued) */
        $que = CustomerQueue::where('customer_id',$cust->id)->whereNull('deactivated_at')->orderBy('created_at','desc')->first() ?: new CustomerQueue;
        $que->customer_id = $cust->id;
        $que->customer_alias = $cust->alias ?: ucwords($cust->first_name).' '.ucwords(substr($cust->last_name ?: '',0,1));
        $que->serviced_at = Carbon::now()->toDateTimeString();
        $que->serviced_by = $user->id;
        $que->deactivated_at = null; // in case customer was deactivated, now their visit is serviced.
        $que->save();

        return $que;

    }


    /* add a customer to queue */
    public function removefromQueue($data,$id,$user=null){

        if(($que = CustomerQueue::find($id))==null) abort(422,'Whoops - We couldnt locate customers record to add to queue - Try again?');

        $que->deactivated_at = Carbon::now()->toDateTimeString();
        $que->save();

        return $que;

    }

    /* clear the queue */
    public function clearQueue($data,$user=null){

        $cleared = 0;
        foreach(CustomerQueue::whereNull('serviced_at')->whereNull('deactivated_at')->get() as $live){
            $live->deactivated_at = Carbon::now()->toDateTimeString();
            $live->save();
            $cleared++;
        }


        return [
            'status'    => 200,
            'message'   => 'Cleared '.$cleared.' Customer from the Queue - However, their visits will still be tracked.',
            'schema'    => Customer::_getSchema()                               // include schema as these batch changes may update the scope of some filters.
        ];

    }





}
