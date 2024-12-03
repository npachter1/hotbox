<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryCampaignsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_campaigns', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

			$table->integer('group_id')->unsigned();					        // Must belongs to 1 customer group
			$table->foreign('group_id')->references('id')->on('dispensary_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
			
			$table->integer('discount_id')->unsigned()->nullable();				// optional hasOne discount_rule (for tracking and prompting a message)
			$table->string('campaign_code', 191)->nullable()->index();			// if discount is attached, then allow input at POS of this code, great for tracking campaign
			$table->string('name', 191)->nullable();							// erp backend reference name
			$table->enum('type',['sms','email','general'])->default('sms')->index(); // type of campaign (used in a switch/case when triggered for different delivery mediums)
			$table->enum('status',['pending','held','working','completed','error','cancelled'])->default('pending')->index(); // current status of campaign
			$table->text('subject')->nullable();								// subject to use for campaign_logs
			$table->text('message')->nullable();								// message content
			
			$table->integer('notified_count')->default(0);						// count aggregates (setAttribute saved)
			$table->integer('visited_count')->default(0);
			$table->integer('purchased_count')->default(0);
			$table->integer('codeused_count')->default(0);

			$table->dateTime('scheduled_at')->nullable()->index();				// if set, campaign will trigger at this utc time

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
        
        Schema::table('dispensary_campaigns', function(Blueprint $table){
			$table->dropForeign(['group_id']);
		});
        Schema::dropIfExists('dispensary_campaigns');
        
    }
    
}