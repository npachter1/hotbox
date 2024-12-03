<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryTaxesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_taxes', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('RESTRICT');
            $table->enum('nature_type', ['recreational','medical','noncannabis','misc'])->index()->nullable();
            
            $table->string('name', 191)->index()->nullable();

            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

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
        
        Schema::table('dispensary_taxes', function(Blueprint $table){
			$table->dropForeign(['created_by']);
			$table->dropForeign(['updated_by']);
		});
        Schema::dropIfExists('dispensary_taxes');
        
    }
    
}