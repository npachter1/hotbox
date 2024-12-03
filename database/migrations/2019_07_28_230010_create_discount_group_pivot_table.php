<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountGroupPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_discount_dispensary_group', function (Blueprint $table) {

            $table->integer('discount_id')->unsigned();
            $table->foreign('discount_id')->references('id')->on('dispensary_discounts')->onDelete('cascade');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('dispensary_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_discount_dispensary_group', function(Blueprint $table){
			$table->dropForeign(['discount_id']);
			$table->dropForeign(['group_id']);
		});
        Schema::drop('dispensary_discount_dispensary_group');
    }

}