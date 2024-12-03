<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePricesetProductPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_priceset_dispensary_product', function (Blueprint $table) {

            $table->integer('priceset_id')->unsigned();
            $table->foreign('priceset_id')->references('id')->on('dispensary_pricesets')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('dispensary_products')->onDelete('cascade');
            
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
        Schema::table('dispensary_priceset_dispensary_product', function(Blueprint $table){
			$table->dropForeign(['priceset_id']);
			$table->dropForeign(['product_id']);
		});
        Schema::drop('dispensary_priceset_dispensary_product');
    }

}