<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryCustomerQueuesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_customer_queues', function (Blueprint $table){
            $table->engine = 'InnoDB';
            
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			$table->integer('customer_id')->unsigned();
			$table->foreign('customer_id')->references('id')->on('dispensary_customers')->onDelete('cascade');
			
			$table->string('customer_alias')->nullable();
			
			$table->uuid('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
 			$table->uuid('serviced_by')->index()->nullable();
            //$table->foreign('serviced_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');         

            $table->dateTime('serviced_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();

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
        
        Schema::table('dispensary_customer_queues', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['customer_id']);
			$table->dropForeign(['created_by']);
			//$table->dropForeign(['serviced_by']);
		});
        Schema::dropIfExists('dispensary_customer_queues');
        
    }
    
}