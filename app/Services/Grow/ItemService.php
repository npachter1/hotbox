<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\ItemCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Item;
use App\Models\Dispensary\CategoryMetrc;
use App\Models\Grow\Strain;

use App\Services\Metrc\MetrcRequestService;
// use App\Jobs\Metrc\Item\MetrcItemStore;
// use App\Jobs\Metrc\Item\MetrcItemUpdate;
// use App\Jobs\Metrc\Item\MetrcItemDelete;

use App\Services\Grow\StrainService;
use App\Helpers\Conversions;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class ItemService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Item::query()
            ->with('metrc_category')
            ->with('strain')
            ->ofTextFilter($search)
            ->ofListFilters($filters)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get item record */
    public function show(array $data,$id){
        
        return Item::query()
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* create item record  */
    public function create($data,$user=null){

        if(!$user) abort(403,'User must be authenticated to proceed');


        /* new record */
        $item = new Item();
        $item->fill($data);
        
        
        // Regulatory (Metrc) conditionals: unit of measure must match the category (each for CountBased, weights for WeightBased)
        if(data_get($user->location,'settings.regulatory_agent','metrc')=='metrc'){
            if(($metrc_category = CategoryMetrc::where('id', data_get($data,'metrc_category_id',null))->first())==null) abort(422,'Metrc category Link unfound');
            elseif ($metrc_category->quantity_type == 'CountBased' && data_get($data,'unit_of_measure',null)!=='ea') abort(422,'Unit of Measure must match category type');
            elseif ($metrc_category->quantity_type == 'WeightBased' && data_get($data,'unit_of_measure',null) == 'ea') abort(422,'Unit of Measure must match category type');
            
            $item->category_type = Item::_parseCategoryType($item->metrc_category_id,$item->name,'metrc');
            $item->metrc_status = 'synced';
        }else{
            $item->category_type = data_get($data,'category_type',Item::_parseCategoryType($item->metrc_category_id,$item->name,data_get($user->location,'settings.regulatory_agent',null)));
            $item->metrc_status = null;
        }
        
        
        $item->created_by = $user->id;
        $item->updated_by = $user->id;
        $item->save();
        $strain = Strain::where('id', $item->strain_id)->first();


        // MetrcItemStore::dispatch($item, $user, $metrc_category, $strain);
        return $item;
        
    }


    /* update item*/
    public function update($data,$id,$user=null){
        
        if(!$user) abort(403,'User must be authenticated to proceed');
        elseif(($item = Item::find($id)) == null) abort(400,'Whoops, Item Data is temporarially disonnected - please try again later');
        elseif((Item::where('name',data_get($data,'name',null))->where('id','!=',$item->id)->count()>=1)) abort(422,'Whoops, Product Name is already in use - aborting');


        $item->fill($data);


        // regulatory conditionals - unit of measure must match the category (each for CountBased, weights for WeightBased)
        if(data_get($user->location,'settings.regulatory_agent','metrc')=='metrc'){
            if(($metrc_category = CategoryMetrc::where('id', data_get($data,'metrc_category_id',null))->first())==null) abort(422,'Metrc category Link unfound');
            elseif ($metrc_category->quantity_type == 'CountBased' && data_get($data,'unit_of_measure',null)!=='ea') abort(422,'Unit of Measure must match category type');
            elseif ($metrc_category->quantity_type == 'WeightBased' && data_get($data,'unit_of_measure',null) == 'ea') abort(422,'Unit of Measure must match category type');
            $item->category_type = Item::_parseCategoryType($item->metrc_category_id,$item->name);
            $item->metrc_status = 'synced';
        }else{
            $item->category_type = data_get($data,'category_type',Item::_parseCategoryType($item->metrc_category_id,$item->name));
            $item->metrc_status = null;
        }
        

        $item->updated_by = $user->id;
        $item->save();
        $strain = Strain::where('id', $item->strain_id)->first();

        
        // MetrcItemUpdate::dispatch($item, $user, $metrc_category, $strain);
        return $item;
        
    }


    public function destroy($data,$id,$user){
        
        if(($item = Item::find($id)) == null) abort(422,'Could not find item to destroy - aborting');

        $item->deleted_by = $user->id;
        $item->metrc_status = 'synced';
        $item->save();
        $item->delete();

        // MetrcItemDelete::dispatch($item,$user);

        return $item->name;
        
    }
    
    
    /* update batch edit data */
    public function updateBatchData($data){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no item batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){
            
            if(!isset($row['id'])) continue;
            elseif(($store = Item::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Item::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }
    
    /* archive batch list */
    public function updateBatchArchive($data){
        
        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no item batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Item::whereIn('id',$batch)->get() as $item){
            
            $item->archived_at = Carbon::now()->toDateTimeString();
            $item->save();
            $saved++;
            
        }
        

        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Item::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    } 
    
    
    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Item_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new ItemCollectionExport($data),$file,$name,$typ,[]);

    }
    
    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::user();

        $item = new Item();
        $item->fill($data);

        $item->metrc_id = $data['Id'];
        $item->name = $data['Name'];
        $item->administration_method = $data['AdministrationMethod'];
        $item->unit_cbd_percent = $data['UnitCbdPercent'];
        $item->unit_cbd_content = $data['UnitCbdContent'];
        $item->unit_thc_percent = $data['UnitThcPercent'];
        $item->unit_thc_content = $data['UnitThcContent'];
        $item->unit_volume = $data['UnitVolume'];
        $item->unit_weight = $data['UnitWeight'];
        $item->serving_size = (strlen($data['ServingSize']) > 0) ? (float)$data['ServingSize'] : null;
        $item->supply_duration_days = $data['SupplyDurationDays'];
        $item->ingredients = $data['Ingredients'];
        
        if ($data['StrainId']) {
            $strain = Strain::where('metrc_id', $data['StrainId'])->first();
            // if Strain not found in Hotbox
            if (!$strain) {
                $strain_api  = new MetrcRequestService();
                $metrc_strain = $strain_api->getStrains($user, $data['StrainId']);
                if ($metrc_strain) {
                    // add Metrc Strain to Hotbox
                    $strain_service = new StrainService();
                    $strain_array = json_decode(json_encode($metrc_strain), true);
                    $strain_service->storeMetrc($strain_array, $user);
                    $new_strain = Strain::where('metrc_id', $data['StrainId'])->first();
                    $item->strain_id = $new_strain->id;
                }
                // if Strain not in Metrc or Hotbox
                else {
                    $item->strain_id = null;
                }
            }
            // else Strain was found in Hotbox
            else
                $item->strain_id = $strain->id;
        }
        
        $metrc_category = CategoryMetrc::where('name', $data['ProductCategoryName'])->where('location_id', $user->location_id)->first();
        if (!$metrc_category) { throw new Exception('Cannot find category for this item.'); }
        $item->metrc_category_id = $metrc_category->id;
        
        $conv = new Conversions;
        $item->unit_of_measure = $conv->uomNameToAbbreviation($data['UnitOfMeasureName']);
        $item->unit_cbd_content_unit_of_measure = $conv->uomNameToAbbreviation($data['UnitCbdContentUnitOfMeasureName']);
        $item->unit_thc_content_unit_of_measure = $conv->uomNameToAbbreviation($data['UnitThcContentUnitOfMeasureName']);
        $item->unit_volume_unit_of_measure = $conv->uomNameToAbbreviation($data['UnitVolumeUnitOfMeasureName']);
        $item->unit_weight_unit_of_measure = $conv->uomNameToAbbreviation($data['UnitWeightUnitOfMeasureName']);

        $item->metrc_status = 'synced';
        $item->location_id = $user->location_id;
        $item->created_by = $user->id;
        $item->updated_by = $user->id;
        $item->save();

        return $item;
    }

}