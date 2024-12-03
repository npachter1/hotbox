<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryCampaignLogsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_campaign_logs', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

			$table->integer('campaign_id')->unsigned();							// belongs to its campaign
			$table->foreign('campaign_id')->references('id')->on('dispensary_campaigns')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->integer('customer_id')->unsigned();							// belongs to customer being notified
			$table->foreign('customer_id')->references('id')->on('dispensary_customers')->onUpdate('CASCADE')->onDelete('CASCADE');
			
			$table->text('response_error')->nullable();							// if there was an error with delivery
			$table->dateTime('scheduled_at')->nullable()->index();				// the time (initiated from campaign) in which to start queing the notification
			$table->dateTime('notified_at')->nullable()->index();				// date of last notification sent (if set to null, will resend)
			$table->dateTime('visited_at')->nullable()->index();					// date of last customer's visit
			$table->dateTime('purchased_at')->nullable()->index();				// date of last customer's purchase
			$table->dateTime('code_used_at')->nullable()->index();				// if customer used a campaign_code [alias to a discount_code] for purchase

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            
            $table->index(['campaign_id', 'notified_at']);						// a composite index to optimize the Campaign set count attributes
			$table->index(['campaign_id', 'visited_at']);
			$table->index(['campaign_id', 'purchased_at']);
			$table->index(['campaign_id', 'code_used_at']);
            
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('dispensary_campaign_logs', function(Blueprint $table){
			$table->dropForeign(['campaign_id']);
			$table->dropForeign(['customer_id']);
		});
        Schema::dropIfExists('dispensary_campaign_logs');
        
    }
    
}