<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryRewardsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_rewards', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
			
			$table->integer('customer_id')->unsigned();
			$table->foreign('customer_id')->references('id')->on('dispensary_customers')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->integer('trigger_id')->unsigned()->nullable()->index();
			//$table->foreign('trigger_id')->references('id')->on('dispensary_reward_triggers')->onUpdate('CASCADE')->onDelete('CASCADE');
			
			$table->integer('sale_id')->unsigned()->nullable();			        // [Optional] Link to a sale order that triggered this transaction
			$table->text('descriptor')->nullable();								// Communicates name of trigger *when* transacted
			$table->text('notes')->nullable();									// Any notes when transacted
			$table->decimal('points_transacted',10,4)->default(0)->index();		// Transaction point value
           
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
        
        Schema::table('dispensary_rewards', function(Blueprint $table){
			$table->dropForeign(['customer_id']);
			//$table->dropForeign(['trigger_id']);
		});
        Schema::dropIfExists('dispensary_rewards');
        
    }
    
}