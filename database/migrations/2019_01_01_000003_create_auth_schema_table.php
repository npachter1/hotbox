<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\AppSchema;


class CreateAuthSchemaTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
            Schema::create('auth_schema', function (Blueprint $table){
                $table->engine = 'InnoDB';
                $table->string('code',100)->primary();
                $table->enum('type',['model','lang','list','registrar','misc'])->default('list');
                $table->mediumText('content')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });

    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        \Storage::put('vault/app_schema_back',AppSchema::get()->toJson());             // store a json file of all the AppSchemas for backup and reload.
        Schema::dropIfExists('auth_schema');
        
    }
    
}
