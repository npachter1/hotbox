<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryCustomersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_customers', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('location_id');                                     // location isnt scoped, just referenced based on whom intook.
            $table->integer('addressbook_id')->nullable();                      // optional addressbook assignment
            $table->string('metrc_id')->nullable();                             // optional metrc id
            $table->enum('type', ['recreational','patient','wholesale','caregiver'])->index()->nullable();
            $table->text('first_name')->nullable();
            $table->text('middle_name')->nullable();
            $table->text('last_name')->nullable();
            $table->string('alias',191)->nullable();                            // incognito or gen smurf name if real name isnt preferred by customer.
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->dateTime('birthdate')->nullable();
            
            $table->string('mmj_card')->nullable();
            $table->char('mmj_card_state', 2)->nullable();
            $table->dateTime('mmj_card_expiry_date')->nullable();
            $table->string('drivers_license')->nullable();
            $table->char('drivers_license_state', 2)->nullable();
            $table->dateTime('drivers_license_expiry_date')->nullable();
            $table->text('document_image')->nullable();
            $table->boolean('tax_exempt')->default(0);
            $table->boolean('email_optin')->default(0);
            $table->boolean('sms_optin')->default(0);
            
            $table->mediumText('settings')->nullable();
            $table->mediumText('preferences')->nullable();
            $table->text('comments')->nullable();

            $table->integer('referral_count')->unsigned()->default(0);
            $table->decimal('total_reward_points',10,4)->default(0.0000);
            $table->decimal('total_spent',10,4)->default(0.0000);
            $table->string('product_ids_purchased')->nullable();
            $table->string('category_ids_purchased')->nullable();
            $table->string('metrc_category_ids_purchased')->nullable();
            $table->integer('total_queue_count')->unsigned()->default(0);

            $table->enum('metrc_status',['confirmed','unconfirmed','exception','new'])->nullable();
			$table->text('metrc_exception')->nullable();

            $table->integer('referred_by')->unsigned()->nullable();             // another customer id for referral reward points..
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
        
        Schema::table('dispensary_customers', function(Blueprint $table){
			$table->dropForeign(['created_by']);
			$table->dropForeign(['updated_by']);
		});
        Schema::dropIfExists('dispensary_customers');
        
    }
    
}