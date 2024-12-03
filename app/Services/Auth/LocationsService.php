<?php

namespace App\Services\Auth;

use App\Services\BaseService;
use App\Mail\LocationModified;

use App\Http\Resources\Auth\LocationsCollectionExport;

use App\Services\Auth\SubscriptionService;

use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\Auth\Subscription;
use App\Models\Auth\Transaction;
use App\Models\Auth\AppLocation;

use App\Helpers\LocationScope;
use App\Helpers\Generator;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use DB;
use PDF;
use Mail;
use Gravatar;


class LocationsService extends BaseService
{
    protected static $PAGINATION_SIZE = 100;
    protected static $MAX_PAGINATION_SIZE = 500;
    
        public function search(array $data,$user=null){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);
        $range = [data_get($data, 'from', null),data_get($data, 'to', null)];

        $query = Location::query()
            ->withCount('users')
            ->whereIn('id',($user ? $user->locationsAssigned()->pluck('id')->toArray() : []))
            ->ofListFilters($filters)
            ->ofTextFilter($search);

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get address record */
    public function show(array $data,$id){
        
        return Location::query()
            ->with('users')
            ->where('id',$id)
            ->first();
            
    }


    /* update address*/
    public function update($data,$id,$user){
        
        if(($location = Location::find($id)) == null) abort(400,'Whoops, Location Data is temporarially disonnected - please try again later');

        $location->fill($data);
        $location->save();
        

        return $location;
        
    }
    
    
    /* Modifying Location data */
    public function modifyAccountStatus($data,$id,$caller){
        
        if(($location = Location::withoutGlobalScope(LocationScope::class)->find($id)) == null) abort(422,'Whoops - Could not update - location registration is temporarially unlinked.');
        
                
        if(($updStatus = data_get($data,'update_status',null))!=$location->status){ // Update status if change
            
            $location->status = $updStatus;
            $location->activated_at = ($location->status=='activated' ? Carbon::now()->toDateTimeString() : null);
            
        }


        $location->save();
        return 'Updated: Location '.$location->id.' Status is '.$location->status;
    
    }
    
    
    /* modify a Location user links */
    public function modifyAccountLinks($data,$id,$caller){
        
        if(($location = Location::withoutGlobalScope(LocationScope::class)->find($id)) == null) abort(422,'Whoops - Could not update - location registration is temporarially unlinked.');
        
        
        $location->users()->sync(data_get($data,'update_user_ids',[]));
        return 'Synced Locations to users.';

    }
 

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Location_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new LocationsCollectionExport($data),$file,$name,$typ,[]);

    }


    public function isMedical($id) {
        $loc = Location::find($id);
        if ($loc->settings->is_medical)
            return 1;
        else
            return 0;
    }

}