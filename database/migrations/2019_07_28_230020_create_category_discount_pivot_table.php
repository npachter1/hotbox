<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoryDiscountPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_category_dispensary_discount', function (Blueprint $table) {

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('dispensary_categories')->onDelete('cascade');
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
        Schema::table('dispensary_category_dispensary_discount', function(Blueprint $table){
			$table->dropForeign(['category_id']);
			$table->dropForeign(['discount_id']);
		});
        Schema::drop('dispensary_category_dispensary_discount');
    }

}