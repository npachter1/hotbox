<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateMetrcCategoriesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('metrc_dispensary_categories', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->string('name',191)->nullable();
			$table->text('product_category_type')->nullable();
			$table->text('quantity_type')->nullable();
			$table->boolean('requires_strain')->default(true);
			$table->boolean('requires_item_brand')->default(false);
			$table->boolean('requires_administration_method')->default(false);
			$table->boolean('requires_unit_cbd_percent')->default(false);
			$table->boolean('requires_unit_cbd_content')->default(false);
			$table->boolean('requires_unit_thc_percent')->default(false);
			$table->boolean('requires_unit_thc_content')->default(false);
			$table->boolean('requires_unit_volume')->default(false);
			$table->boolean('requires_unit_weight')->default(false);
			$table->boolean('requires_serving_size')->default(false);
			$table->boolean('requires_supply_duration_days')->default(false);
			$table->boolean('requires_ingredients')->default(false);
			$table->boolean('requires_product_photo')->default(false);
			$table->boolean('can_contain_seeds')->default(false);
			$table->boolean('can_be_remediated')->default(false);
			$table->float('thc_equiv_ratio', 12, 6)->default(1.000000);
			$table->float('cbd_equiv_ratio', 12, 6)->default(1.000000);
			$table->enum('thc_equiv_prompt', array('weight','potency','amount'))->default('amount')->nullable();
			$table->text('public_img')->nullable();
            $table->boolean('display_pos')->default(true);
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
        
        Schema::table('metrc_dispensary_categories', function(Blueprint $table){
			$table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('metrc_dispensary_categories');
        
    }
    
}