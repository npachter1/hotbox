<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\DrawerCollectionExport;

use App\Models\Dispensary\Drawer;
use App\Models\Dispensary\DrawerEvent;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class DrawerService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Drawer::query()
            ->with('esum')
            ->withCount('user','events','sales')
            ->ofListFilters($filters);

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get Drawer record */
    public function show(array $data,$id){
        
        return Drawer::query()
            ->with('user','events','sales.payments','sales.items.inventory.product')
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create Drawer record  */
    public function create($data,$user=null){
        
        if($this->_currentDrawer($data,$user)!=null) abort(409,'This User already has an open cash drawer');

        $drawer = new Drawer();
        $drawer->user_id = $user->id;
        $drawer->opening_balance = $this->_getCashDrawerTotal($data);
        $drawer->current_balance = $drawer->opening_balance;

        $drawer->save();
        $drawerEvent = $this->_storeCashDrawerEvent($drawer->id, 'open', $data);

        // Re-query this cash drawer events:
        $drawer->load('events');

        return $drawer;
        
    }


    /* update Drawer */
    public function update($data,$id,$user=null){
        
        if(($upd = Drawer::find($id)) == null) abort(400,'Whoops, Drawer Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        

        return $upd;
        
    }
    
    /* update Drawer */
    public function reassign($data,$id,$user=null){
        
        if(($upd = Drawer::find($id)) == null) abort(400,'Whoops, Drawer Data is temporarially disonnected - please try again later');

        $upd->user_id = data_get($data,'user_id',$upd->user_id);
        
        $upd->opening_balance = data_get($data,'opening_balance',0);
        $upd->current_balance = data_get($data,'current_balance',0);
        $upd->closing_balance = data_get($data,'closing_balance',0);
        
        
        $upd->save();
        return $upd;
        
    }
    

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Drawer_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new DrawerCollectionExport($data),$file,$name,$typ,[]);

    }
    

    /* Removal service */
    public function archive($data,$id,$user){
        
        if(($record = Drawer::find($id)) == null) abort(422,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->closed_at = Carbon::now()->toDateTimeString();
        $record->save();        
        //$record->delete();                                                    // A soft-delete

        return true;
        
    }

    
    /* PayIn to Drawer */
    public function payIn($data,$id,$user=null){
        
        if(($upd = Drawer::find($id)) == null) abort(400,'Whoops, Drawer Data is temporarially disonnected - please try again later');
        elseif($upd->closed_at) abort(422,'Cannot modify a closed cash drawer');

        $upd->current_balance = $upd->current_balance + $this->_getCashDrawerTotal($data);
        $upd->save();

        $cashDrawerEvent = $this->_storeCashDrawerEvent($upd->id, 'pay-in', $data);

        // Re-query this cash drawer events:
        $upd->load('events');
        return $upd;
        
    }

    /* PayOut of the Drawer */
    public function payOut($data,$id,$user=null){
        
        if(($upd = Drawer::find($id)) == null) abort(400,'Whoops, Drawer Data is temporarially disonnected - please try again later');
        elseif($upd->closed_at) abort(422,'Cannot modify a closed cash drawer');

        $upd->current_balance = $upd->current_balance - $this->_getCashDrawerTotal($data);
        $upd->save();

        $cashDrawerEvent = $this->_storeCashDrawerEvent($upd->id, 'pay-out', $data);

        // Re-query this cash drawer events:
        $upd->load('events');
        return $upd;
        
    }

    /* Close the Drawer */
    public function close($data,$id,$user=null){
        
        if(($upd = Drawer::find($id)) == null) abort(400,'Whoops, Drawer Data is temporarially disonnected - please try again later');
        elseif($upd->closed_at) abort(422,'Cannot modify a closed cash drawer');

        $upd->closing_balance = $this->_getCashDrawerTotal($data);
        $upd->closed_at = Carbon::now()->toDateTimeString();
        $upd->save();

        $cashDrawerEvent = $this->_storeCashDrawerEvent($upd->id, 'close', $data);

        // Re-query this cash drawer events:
        $upd->load('events');
        return $upd;
        
    }
    


    
    /**                                             **/
	/**            Drawer Routines                  **/
	/**                                             **/        

    /* get users current drawer */
    public function _currentDrawer(array $data,$user){
        
        return Drawer::with('events')
            ->where('user_id',$user->id)
            ->whereNull('closed_at')
            ->orderBy('created_at','asc')
            ->first();                                                          // Only return a single, open cash drawer with its events:
    
    }    


    protected function _getCashDrawerTotal(array $data) {
        $total = 0.00;

        // Calculate in cents, first, to avoid floating point precision issues
        $total += (float)data_get($data,'bill_1', 0) * 100;
        $total += (float)data_get($data,'bill_5', 0) * 5 * 100;
        $total += (float)data_get($data,'bill_10', 0) * 10 * 100;
        $total += (float)data_get($data,'bill_20', 0) * 20 * 100;
        $total += (float)data_get($data,'bill_50', 0) * 50 * 100;
        $total += (float)data_get($data,'bill_100', 0) * 100 * 100;

        // Coins are already in cents:
        $total += (float)data_get($data,'coin_1', 0);
        $total += (float)data_get($data,'coin_5', 0) * 5;
        $total += (float)data_get($data,'coin_10', 0) * 10;
        $total += (float)data_get($data,'coin_25', 0) * 25;
        $total += (float)data_get($data,'coin_50', 0) * 50;

        // Extra is an exact amount in dollars:
        $total += (float)data_get($data,'extra', 0) * 100;

        return ($total / 100);
    }
    
    
    protected function _storeCashDrawerEvent($drawerId, $type, array $data) {
        $cashDrawerEvent = new DrawerEvent();

        $cashDrawerEvent->drawer_id = $drawerId;
        $cashDrawerEvent->event_type = $type;
        $cashDrawerEvent->bill_1 = (float)data_get($data,'bill_1', 0);
        $cashDrawerEvent->bill_5 = (float)data_get($data,'bill_5', 0);
        $cashDrawerEvent->bill_10 = (float)data_get($data,'bill_10', 0);
        $cashDrawerEvent->bill_20 = (float)data_get($data,'bill_20', 0);
        $cashDrawerEvent->bill_50 = (float)data_get($data,'bill_50', 0);
        $cashDrawerEvent->bill_100 = (float)data_get($data,'bill_100', 0);
        $cashDrawerEvent->coin_1 = (float)data_get($data,'coin_1', 0);
        $cashDrawerEvent->coin_5 = (float)data_get($data,'coin_5', 0);
        $cashDrawerEvent->coin_10 = (float)data_get($data,'coin_10', 0);
        $cashDrawerEvent->coin_25 = (float)data_get($data,'coin_25', 0);
        $cashDrawerEvent->coin_50 = (float)data_get($data,'coin_50', 0);
        $cashDrawerEvent->extra = (float)data_get($data,'extra', 0);
        $cashDrawerEvent->total = $this->_getCashDrawerTotal($data);
        $cashDrawerEvent->save();

        return $cashDrawerEvent;
    }
    



}