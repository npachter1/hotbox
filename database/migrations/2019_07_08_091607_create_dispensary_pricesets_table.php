<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryPricesetsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_pricesets', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
			
			$table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('RESTRICT');
			
			$table->string('category_type',191)->index()->default('merchandise'); // pricing set filters/scopes to the category type
			//$table->foreign('category_id')->references('id')->on('dispensary_categories')->onDelete('cascade');
			
			$table->string('name_grade',200)->nullable();						// set title, also referred to as grade
			$table->enum('type_uom',['g','mg','ea','misc'])->nullable();
			$table->decimal('amount_default', 10, 4)->unsigned()->nullable();	// default fixed price amount
			$table->text('amount_tiers')->nullable();							// tiers casted object, based on type
			$table->integer('rank')->unsigned()->default(99);					// rank (1 = highest, 99 = lowest) in selecting defaults
			$table->boolean('is_active')->default(1);							// toggle whether this pricing is active or not.
			$table->boolean('is_default')->default(1);							// is this set a default of the category, or does it belong to specific products.
			$table->text('notes_announcement')->nullable();						// any descriptor or announcement to show when this pricing is selected (pos - notes)
           
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
        
        Schema::table('dispensary_pricesets', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			//$table->dropForeign(['category_id']);
		});
        Schema::dropIfExists('dispensary_pricesets');
        
    }
    
}