<?php

namespace App\Services\Auth;

use App\Services\BaseService;

use App\Http\Resources\Auth\ServicelogCollectionExport;

use App\Models\Auth\Servicelog;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use Excel;
use Storage;


class ServicelogService extends BaseService
{
    
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;

    /**                                         **/
    /**      (superadmin/servicelog grid view)  **/
	/**                                         **/


    public function search(array $data){
        
        $page = (int)data_get($data, 'page', null);
        $limit = (int)data_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = data_get($data, 'sort', null);

        $search = data_get($data, 'search', null);
        $filters = data_get($data,'filter',[]);

        $query = Servicelog::query()
            ->ofListFilters($filters)
            ->ofTextFilter($search)
            ->ofActive();

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('updated_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get address record */
    public function show(array $data,$id){
        
        return Servicelog::query()
            ->where('id',$id)
            ->first();
            
    }


    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Servicelog_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;
        
        
        return $this->export(new ServicelogCollectionExport($data),$file,$name,$typ,[]);

    }
    

}