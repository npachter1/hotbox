<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterDispensaryCampaignsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        DB::statement("ALTER TABLE `dispensary_campaigns` CHANGE `discount_id` `discount_id` INT(10) UNSIGNED NULL;");
        
        Schema::table('dispensary_campaigns', function (Blueprint $table){
 			//$table->integer('discount_id')->nullable()->change();				// change discount_id to be nullable
			$table->string('sms_test_number',191)->nullable();                  // add a test number for the campaign (for test mode vs texting real numbers)
			$table->text('delivery_status')->nullable();                        // message field of latest delivery job
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        

    }
    
}