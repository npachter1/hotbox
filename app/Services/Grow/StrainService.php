<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\StrainCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Strain;
use App\Models\Grow\StrainLog;

// use App\Services\Metrc\MetrcRequestService;
// use App\Jobs\Metrc\Strain\MetrcStrainStore;
// use App\Jobs\Metrc\Strain\MetrcStrainUpdate;
// use App\Jobs\Metrc\Strain\MetrcStrainDelete;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class StrainService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Strain::query()
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get strain record */
    public function show(array $data,$id){

        return Strain::query()
            ->where('id',$id)
            ->first();

    }


    /* create strain record  */
    public function create($data){

        $user = Auth::guard()->user();
        $strain = Strain::where('name', $data['name'])->where('location_id', $user->location_id)->get();
        if (count($strain) > 0)
            throw new Exception('Strain with name'.$data["name"].' already exists');

        /* new record */
        $strain = new Strain();
        $strain->fill($data);
        $strain->metrc_status = 'synced';
        $strain->location_id = $user->location_id;
        $strain->created_by = $user->id;
        $strain->updated_by = $user->id;
        $strain->save();

        // MetrcStrainStore::dispatch($strain, $user);

        return $strain;

    }


    /* update strain*/
    public function update($data,$id){

        if(($upd = Strain::find($id)) == null) abort(400,'Whoops, Strain Data is temporarially disonnected - please try again later');

        $user = Auth::guard()->user();

        $strain = Strain::find($id);
        if (!$strain) { throw new Exception('Item not found.'); }

        // if strain name updated, check if new name already exists
        if ($strain->name !== $data['name']) {
            $strain2 = Strain::where('name', $data['name'])->where('location_id', $user->location_id)->get();
            if (count($strain2) > 0)
                throw new Exception('Strain with name '.$data['name'].' already exists');
        }


        $strainLog = new StrainLog();  // record previous data in strain log
        $strainLog->fill(json_decode(json_encode($strain), true));
        $strainLog->created_by = $user->id;
        $strainLog->save();

        $strain->fill($data);

        $strain->metrc_status = 'synced';
        $strain->updated_by = $user->id;
        $strain->save();

        // MetrcStrainUpdate::dispatch($strain, $user);

        return $strain;

    }


    public function destroy($data,$id,$user){

        if(($strain = Strain::find($id)) == null) abort(422,'Could not find Strian to destroy - aborting');

        $strain->deleted_by = $user->id;
        $strain->metrc_status = 'synced';
        $strain->save();
        $strain->delete();

        // MetrcStrainDelete::dispatch($strain,$user);

        return $strain->name;

    }


    /* update batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no strain batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Strain::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Strain::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no strain batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Strain::whereIn('id',$batch)->get() as $strain){

            $strain->archived_at = Carbon::now()->toDateTimeString();
            $strain->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Strain::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Strain_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new StrainCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::guard()->user();


        $strain = ( Strain::where('metrc_id',$data['Id'])->first() ?: new Strain );
        $strain->fill($data);

        $strain->metrc_id = $data['Id'];
        $strain->name = $data['Name'];
        $strain->testing_status = $data['TestingStatus'];
        $strain->thc_level = $data['ThcLevel'];
        $strain->cbd_level = $data['CbdLevel'];
        $strain->indica_percentage = $data['IndicaPercentage'];
        $strain->sativa_percentage = $data['SativaPercentage'];

        $strain->metrc_status = 'synced';
        $strain->location_id = $user->location_id;
        $strain->created_by = $user->id;
        $strain->updated_by = $user->id;
        $strain->save();

        return $strain;
    }

}
