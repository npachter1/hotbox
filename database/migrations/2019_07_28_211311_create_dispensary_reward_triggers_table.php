<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryRewardTriggersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_reward_triggers', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
			
			$table->enum('type',['sale','referral','revisit','admin','misc'])->nullable();
			$table->text('descriptor')->nullable();								// Communicates name of trigger *when* transacted
			$table->text('notes')->nullable();									// Any notes when transacted
			$table->decimal('point_amount',10,4)->default(0);		            // Transaction point value
           
            $table->boolean('is_exclusive')->default(true);
            $table->boolean('is_active')->default(true);
           
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
        
        Schema::table('dispensary_reward_triggers', function(Blueprint $table){

		});
        Schema::dropIfExists('dispensary_reward_triggers');
        
    }
    
}