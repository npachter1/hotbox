<?php

namespace App\Services\Auth;

use App\Services\BaseService;

use App\Http\Resources\Auth\AddressbookCollectionExport;

use App\Models\Auth\Addressbook;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class AddressbookService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Addressbook::query()
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get address record */
    public function show(array $data,$id){

        return Addressbook::query()
            ->where('id',$id)
            ->first();

    }


    /* create address record  */
    public function create($data){

        /* get same name/address1/zip or create new! */
        $addr = new Addressbook();
        $addr->fill($data);
        $addr->save();


        return $addr;

    }


    /* get a batch (abbreviated) collection */
    public function getBatch(array $data){

        $ids = explode(',',data_get($data,'batch_ids',''));

        $query = Addressbook::query()
            ->select('id','type','name','address1','address2','city','region','country','email')
            ->whereIn('id',$ids)
            ->orderBy('updated_at')
            ->ofActive();


        return $query->get();

    }


    /* update address*/
    public function update($data,$id){

        if(($upd = Addressbook::find($id)) == null) abort(400,'Whoops, Addressbook Data is temporarially disonnected - please try again later');

        $upd->fill($data);
        $upd->save();


        return $upd;

    }


    /* update batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no address batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Addressbook::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Addressbook::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no address batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Addressbook::whereIn('id',$batch)->get() as $store){

            $store->archived_at = Carbon::now()->toDateTimeString();
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Addressbook::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Addressbook_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new AddressbookCollectionExport($data),$file,$name,$typ,[]);

    }


    /* removal service */
    public function archive($data,$id,$user){

        if(($record = Addressbook::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();
        //$record->delete();                                                    // A soft-delete

        return true;

    }

}
