<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;

use Carbon\Carbon;


trait FilterScopes
{
    
    /* filter by matched value or all */
    public function scopeOfMatchFilter($query, $filter,$name='status'){
        
        if(Schema::hasColumn($this->getTable(),$name)==false) return $query;
        elseif(!$filter || in_array(strtolower($filter),['','null','undefined',null,'all'])) return $query;


        return $query->where($name,$filter);
        
    }
    
    
    /* filter by frontend query list of values */
    public function scopeOfListFilters($query,$filters){
        
        if(!$filters) return $query;

        foreach($filters as $field => $list){
            if(Schema::hasColumn($this->getTable(),$field)==false) continue;
            elseif(!$list || $list=='all') continue;
            elseif(in_array($field,$this->getDates())){
                $dates = explode(',',$list);
                if(isset($dates[1]))
                    $query->whereBetween($field,[Carbon::create($dates[0])->toDateTimeString(),Carbon::create($dates[1])->toDateTimeString()]);
            }else $query->whereIn($field,explode(',',$list));
        }
        
        
        return $query;

    }

    
    /* filter by frontend query orderBy filter */
    public function scopeOfOrderByFilter($query,$sorts){
        
        if(!$sorts || $sorts=='null') return $query;

        foreach((explode(',',$sorts)) as $sorting){                             // sort preference is in order it came in.
            $field = (preg_match('/-\w/',$sorting) ? preg_replace('/-/','',$sorting) : $sorting);
            if(Schema::hasColumn($this->getTable(),$field)==false) continue;
            elseif(preg_match('/-\w/',$sorting)) $query->orderBy($field,'desc');
            else $query->orderBy($sorting,'asc');
        }

        return $query;
    }
    
    public function scopeOfHasFilter($query,$rel){
        
        if(!$rel || $rel=='all') return $query;
        return $query->whereHas($rel);
        
    }
    
    
    /* filter based on a belongsToMany association with ids passed */
    public function scopeOfAssociationFilter($query,$rel,$filter){

        if(!$filter || in_array(strtolower($filter),['','null','undefined',null,'all'])) return $query;
        elseif(empty(($ids = explode(',',$filter)))) return $query;
        else{
            
            $ids = array_diff($ids,['all']); // if "all" is in the list of ids
            
            return $query->whereHas($rel,function($q)use($ids){
                $q->whereIn('id', $ids);
            });
            
        }

    }
    

}