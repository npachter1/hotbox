<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryInventoryLogTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_inventory_logs', function (Blueprint $table){
            $table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('inventory_id')->unsigned();
			$table->foreign('inventory_id')->references('id')->on('dispensary_inventories')->onDelete('cascade');
			$table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			$table->enum('type',['quantity','sale']);
			$table->text('value')->nullable();							
			$table->string('reason_code',191)->nullable(); // map to metrc reason codes
			$table->text('notes')->nullable();
			$table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error','pending'))->nullable();
			$table->dateTime('metrc_synced_at')->nullable();
			$table->dateTime('reviewed_at')->nullable();
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
        
        Schema::table('dispensary_inventory_logs', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['inventory_id']);
		});
        Schema::dropIfExists('dispensary_inventory_logs');
        
    }
    
}