<?php

namespace App\Services\Dispensary;

use App\Services\BaseService;

use App\Http\Resources\Dispensary\TaxCollectionExport;

use App\Models\Dispensary\Tax;
use App\Models\Dispensary\TaxRate;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class TaxService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Tax::query()
            ->with('rates')
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get tax group record */
    public function show(array $data,$id){
        
        return Tax::query()
            ->with('rates')
            ->where('id',$id)
            ->first();
            
    }

    /* show taxrate form */
    public function rateShow(array $data,$id){
        
        return TaxRate::query()
            ->where('id',$id)
            ->first();
            
    }
   
    
    /* create tax record  */
    public function create($data,$user=null){
        
        /* new record */
        $add = new Tax();
        $add->fill($data);
        $add->save();
        
        $add->rates()->sync(data_get($data,'rate_ids',[]));

        return $add;
        
    }
    
    /* create tax record  */
    public function rateCreate($data,$user=null){
        
        /* new record */
        $add = new TaxRate();
        $add->fill($data);
        $add->save();
        
        return $add;
        
    }


    /* update tax*/
    public function update($data,$id,$user=null){
        
        if(($upd = Tax::find($id)) == null) abort(400,'Whoops, Tax Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        
        $upd->rates()->sync(data_get($data,'rate_ids',[]));
        

        return $upd;
        
    }

    public function rateUpdate($data,$id,$user=null){
        
        if(($upd = TaxRate::find($id)) == null) abort(400,'Whoops, Tax Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();
        

        return $upd;
        
    }

    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Tax_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new TaxCollectionExport($data),$file,$name,$typ,[]);

    }
    
    
    /* removal service */
    public function archive($data,$id,$user){
        
        if(($record = Tax::find($id)) == null) abort(409,'Cannot Archive - No Record found');
        elseif(($has = Tax::where('nature_type',$record->nature_type)->where('location_id',$record->location_id)->ofActive()->count()) <= 1) abort(422,'Hold up - This is the Only Tax Group for your locations '.$record->nature_type.' products - thus you cannot archive!');

                
        $record->archived_at = Carbon::now()->toDateTimeString();               // Archive instead - wont show up in grid view but can be queried for backend functions
        $record->save();        
        

        return true;
        
    }
    
    
    
    /* service to sync dispensary_tax_rates table with latest and greatest */
    public function _syncTaxRates(){
        
        // TODO create a service to update the dispensary_tax_rates table with latest and greatest tax rates for each state.
        // This method is called via the scheduler in the php artisan::housekeep command routine nightly.
        
        
        
        
        
        return true;
        
    }
    

}