<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoryPricesetPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_category_dispensary_priceset', function (Blueprint $table) {

            $table->integer('priceset_id')->unsigned();
            $table->foreign('priceset_id')->references('id')->on('dispensary_pricesets')->onDelete('cascade');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('dispensary_categories')->onDelete('cascade');
            
            $table->integer('rank')->default(99);                                // TODO if the rates assigned to the category need to be ranked..

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_category_dispensary_priceset', function(Blueprint $table){
			$table->dropForeign(['priceset_id']);
			$table->dropForeign(['category_id']);
		});
        Schema::drop('dispensary_category_dispensary_priceset');
    }

}