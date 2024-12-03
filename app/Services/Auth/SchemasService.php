<?php

namespace App\Services\Auth;

use App\Services\BaseService;

use App\Http\Resources\Auth\SuitesCollectionExport;

use App\Models\Auth\User;
use App\Models\AppSchema;

use Auth;
use Exception;
use App\Helpers\Generator;
use stdClass;
use Carbon\Carbon;
use DB;
use PDF;
use Mail;
use Gravatar;


class SchemasService extends BaseService
{

    public function list(array $data){

        /*return AppSchema::select('code','type','content')
            ->whereNotNull('content')
            ->orderBy('code')
            ->get();*/

        if(config('app.env') != 'local') return null; // editable schema list ONLY for local enveironment - developers will push updates to schema which is now in source code.
        else{
            
            $schemas = [];
            foreach(scandir(database_path('schemas/')) as $file)
                if(($schema = json_decode(file_get_contents(database_path('schemas/'.$file)))) != null)
                    if($schema->content)
                        $schemas[$schema->type][] = $schema; // get schema from source code database/schemas/[code].json
    
    
            return $schemas;
        
        }
        
    }
    

    /* update schema file */
    public function update($data,$code){
        
        $appSchema = AppSchema::where('code',$code)->first() ?: new AppSchema();

        $appSchema->fill($data);
        $appSchema->code = data_get($data,'codename',null);
        $appSchema->content = json_decode(data_get($data,'content',null));
        $appSchema->save(); // a local db copy
        
        $fp = fopen(database_path('schemas/'.$code.'.json'), 'w');
        fwrite($fp, json_encode($appSchema));
        fclose($fp);
        

        return $appSchema;
        
    }


}