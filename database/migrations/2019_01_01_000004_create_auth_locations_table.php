<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateAuthLocationsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('auth_locations', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->integer('id')->unique();
            $table->integer('addressbook_id')->nullable();                      // optional addressbook assignment
            $table->enum('type',['grow','dispensary','vendor','producer','processor','test'])->nullable();
            $table->string('licensenum')->nullable();
            $table->string('name',191)->nullable();
            $table->text('thumb')->nullable();
            $table->enum('status', ['registered','activated','inactivate','migrating','denied','requested','held'])->nullable();
            $table->boolean('is_demo')->default(0);
            $table->text('associated_licensenums')->nullable();
            $table->mediumText('settings')->nullable();
            $table->mediumText('migration_settings')->nullable();
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::dropIfExists('auth_locations');
        
    }
    
}
