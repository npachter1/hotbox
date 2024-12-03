<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountProductPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_discount_dispensary_product', function (Blueprint $table) {

            $table->integer('discount_id')->unsigned();
            $table->foreign('discount_id')->references('id')->on('dispensary_discounts')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('dispensary_products')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_discount_dispensary_product', function(Blueprint $table){
			$table->dropForeign(['discount_id']);
			$table->dropForeign(['product_id']);
		});
        Schema::drop('dispensary_discount_dispensary_product');
    }

}