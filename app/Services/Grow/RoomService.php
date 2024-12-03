<?php

namespace App\Services\Grow;

use App\Services\BaseService;

use App\Http\Resources\Grow\RoomCollectionExport;

use App\Models\Auth\User;
use App\Models\Grow\Room;

// use App\Services\Metrc\MetrcRequestService;
// use App\Jobs\Metrc\Room\MetrcRoomStore;
// use App\Jobs\Metrc\Room\MetrcRoomUpdate;
// use App\Jobs\Metrc\Room\MetrcRoomDelete;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class RoomService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function search(array $data){

        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Room::query()
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);

    }


    /* get room record */
    public function show(array $data,$id){

        return Room::query()
            ->where('id',$id)
            ->first();

    }


    /* create room record  */
    public function create($data){

        $user = Auth::guard()->user();
        $room = Room::where('name', $data['name'])->where('location_id', $user->location_id)->get();
        if (count($room) > 0)
            throw new Exception('Room with name'.$data["name"].' already exists');

        /* new record */
        $room = new Room();
        $room->metrc_status = 'synced';
        $room->name = $data['name'];
        $room->type = $data['type'];
        $room->location_id = $user->location_id;
        $room->created_by = $user->id;
        $room->updated_by = $user->id;
        $room->save();

        // MetrcRoomStore::dispatch($room, $user);

        return $room;

    }


    /* update room*/
    public function update($data,$id){

        if(($upd = Room::find($id)) == null) abort(400,'Whoops, Room Data is temporarially disonnected - please try again later');

        $user = Auth::guard()->user();

        // check if new room name already exists
        $room = Room::where('name', $data['name'])->where('location_id', $user->location_id)->get();
        if (count($room))
            if ($room[0]->id !== $data['id'])
                throw new Exception('Room with name'.$data["name"].' already exists');

        $room = Room::find($id);
        if (!$room) { throw new Exception('Item not found.'); }
        $room->name = $data['name'];
        $room->type = $data['type'];
        $room->metrc_status = 'synced';
        $room->updated_by = $user->id;
        $room->save();

        // MetrcRoomUpdate::dispatch($room, $user);

        return $room;

    }


    public function destroy($data,$id,$user){

        if(($room = Room::find($id)) == null) abort(422,'Could not find Room to destroy - aborting');

        $room->deleted_by = $user->id;
        $room->metrc_status = 'synced';
        $room->save();
        $room->delete();

        // MetrcRoomDelete::dispatch($room,$user);

        return $room->name;

    }


    /* update batch edit data */
    public function updateBatchData($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no room batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach($batch as $row){

            if(!isset($row['id'])) continue;
            elseif(($store = Room::find($row['id'])) == null) continue;

            $store->fill($row);
            $store->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Updated '.$saved.' of '.count($batch).' Records - Please check updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Room::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }

    /* archive batch list */
    public function updateBatchArchive($data){

        if(($batch = data_get($data,'batch',null)) == null) abort(422,'Whoops, no room batch data posted - aborting.');
        elseif(!is_array($batch)) abort(412,'Whoops, bad batch data post - aborting.');

        $saved = 0;
        foreach(Room::whereIn('id',$batch)->get() as $room){

            $room->archived_at = Carbon::now()->toDateTimeString();
            $room->save();
            $saved++;

        }


        return [
            'status'    => ($saved==count($batch) ? 200 : 207),
            'message'   => ($saved!=count($batch) ? 'Archived '.$saved.' of '.count($batch).' Records - Please review updates below..' : 'Successfully Updated '.$saved.' of '.count($batch).' Records!'),
            'schema'    => Room::_getSchema()                            // include schema as these batch changes may update the scope of some filters.
        ];

    }


    /* process an export */
    public function exportCollection($data,$typ,$file){

        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Room_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all')
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new RoomCollectionExport($data),$file,$name,$typ,[]);

    }

    public function storeMetrc(array $data, $user=null)
    {
        if (!$user)
            $user = Auth::user();

        $room = ( Room::where('metrc_id',$data['Id'])->first() ?: new Room );

        $room->metrc_id = $data['Id'];
        $room->name = $data['Name'];
        $room->metrc_status = 'synced';
        $room->location_id = $user->location_id;
        $room->created_by = $user->id;
        $room->updated_by = $user->id;
        $room->save();

        return $room;
    }

}
