<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountPricesetPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_discount_dispensary_priceset', function (Blueprint $table) {

            $table->integer('priceset_id')->unsigned();
            $table->foreign('priceset_id')->references('id')->on('dispensary_pricesets')->onDelete('cascade');
            $table->integer('discount_id')->unsigned();
            $table->foreign('discount_id')->references('id')->on('dispensary_discounts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_discount_dispensary_priceset', function(Blueprint $table){
			$table->dropForeign(['priceset_id']);
			$table->dropForeign(['discount_id']);
		});
        Schema::drop('dispensary_discount_dispensary_priceset');
    }

}