<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryGroupsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_groups', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();			
			
			$table->string('name',191)->nullable();
			$table->text('notes')->nullable();
			$table->enum('type',['auto','custom'])->nullable();					// if auto, apply filters upon save of a customer, if custom, customers are user assigned
			$table->mediumText('filters')->nullable();							// object casted search filters used to generate group from customers
			
			$table->dateTime('last_synced_at')->nullable();						// last sync of auto-filters
			$table->dateTime('archived_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('dispensary_groups', function(Blueprint $table){

		});
        Schema::dropIfExists('dispensary_groups');
        
    }
    
}