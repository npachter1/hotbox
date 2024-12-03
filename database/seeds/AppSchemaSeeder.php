<?php

use Database\DisableForeignKeys;
use Database\TruncateTable;
use Illuminate\Database\Seeder;

use App\Models\AppSchema;


/**
 * Class UsersTableSeeder.
 */
class AppSchemaSeeder extends Seeder
{
    
    use DisableForeignKeys, TruncateTable;


    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run(){
        
        $this->disableForeignKeys();
        $this->truncate('auth_schema');

        /*if($data = json_decode(\Storage::get('vault/app_schema_back'))){        // latest production back in vault (if you need)
            foreach($data as $row)
                if(!AppSchema::find($row->code))
                    factory(AppSchema::class)->create([
                        'code'              => $row->code,
                        'type'              => $row->type,
                        'content'           => $row->content,
                        'created_at'        => $row->created_at
                    ]);
        }*/
        
        
        if($seed = json_decode(file_get_contents(database_path('data').'/app_schema_seed.json'))){
            foreach($seed as $row)
                if(!AppSchema::find($row->code))                                // then, fill with any included data from schema_init_seed
                    factory(AppSchema::class)->create([
                        'code'              => $row->code,
                        'type'              => $row->type,
                        'content'           => $row->content,
                        'created_at'        => $row->created_at
                    ]);
        }
        

        $this->enableForeignKeys();
        
    }
    
}
