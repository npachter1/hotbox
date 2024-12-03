<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryDiscountsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_discounts', function (Blueprint $table){
            
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

			$table->string('name', 191)->nullable();
			$table->text('descriptor')->nullable();								// Communicates the discount made to the line item for POS and receipts
			$table->enum('type',['loyalty','time','general'])->default('general')->index();
			$table->enum('discount_type',['pct','amt'])->default('amt');		// Is discount_amount a percentage off or amount (TODO BOGO - for less if allowed)
			$table->decimal('discount_amount',10,4)->nullable();
			$table->string('discount_code', 250)->nullable()->index();			// Optional customer inputted code to unlock - if null, auto-apply based on type/settings
			$table->enum('distribution_type',['equal','proportionate','line']);	// Override (from schema) how to distribute, per line item assigned, discount applied
			$table->mediumText('settings')->nullable();							// Object casted things to filter, based on discount_rules_schema.settings.{type}
			$table->integer('rank')->default(10);								// Sort rules by this number
			$table->boolean('is_exclusive')->default(0);						// If set to 1, stop further rules processing
			$table->integer('max_per_customer')->nullable();					// how many times a single customer can use
			$table->integer('is_active')->default(1);							// activation boolean

    		$table->dateTime('scheduled_at')->nullable();					    // date the discount rule can be used
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
        
        Schema::table('dispensary_discounts', function(Blueprint $table){

		});
        Schema::dropIfExists('dispensary_discounts');
        
    }
    
}