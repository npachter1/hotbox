<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryDrawerEventsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_drawer_events', function (Blueprint $table){
            $table->engine = 'InnoDB';
            
            $table->increments('id')->unsigned();
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			
			$table->integer('drawer_id')->unsigned();
			$table->foreign('drawer_id')->references('id')->on('dispensary_drawers')->onDelete('cascade');
			
			$table->enum('event_type', array('open','close','pay-in','pay-out'));
			$table->smallInteger('bill_1')->unsigned()->default(0);
			$table->smallInteger('bill_5')->unsigned()->default(0);
			$table->smallInteger('bill_10')->unsigned()->default(0);
			$table->smallInteger('bill_20')->unsigned()->default(0);
			$table->smallInteger('bill_50')->unsigned()->default(0);
			$table->smallInteger('bill_100')->unsigned()->default(0);
			$table->smallInteger('coin_1')->unsigned()->default(0);
			$table->smallInteger('coin_5')->unsigned()->default(0);
			$table->smallInteger('coin_10')->unsigned()->default(0);
			$table->smallInteger('coin_25')->unsigned()->default(0);
			$table->smallInteger('coin_50')->unsigned()->default(0);
			$table->decimal('extra', 10, 4)->unsigned()->default(0.0000);
			$table->decimal('total', 10, 4)->unsigned()->default(0.0000);
			
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
        
        Schema::table('dispensary_drawer_events', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['drawer_id']);
		});
        Schema::dropIfExists('dispensary_drawer_events');
        
    }
    
}