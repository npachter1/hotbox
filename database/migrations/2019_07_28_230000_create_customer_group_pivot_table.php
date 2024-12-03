<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerGroupPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_customer_dispensary_group', function (Blueprint $table) {

            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('dispensary_customers')->onDelete('cascade');
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
        Schema::table('dispensary_customer_dispensary_group', function(Blueprint $table){
			$table->dropForeign(['customer_id']);
			$table->dropForeign(['group_id']);
		});
        Schema::drop('dispensary_customer_dispensary_group');
    }

}