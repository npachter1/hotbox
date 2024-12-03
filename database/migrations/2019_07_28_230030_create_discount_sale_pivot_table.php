<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiscountSalePivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_discount_dispensary_sale', function (Blueprint $table) {

            $table->integer('discount_id')->unsigned();
            $table->foreign('discount_id')->references('id')->on('dispensary_discounts')->onDelete('cascade');
            $table->integer('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('dispensary_sales')->onDelete('cascade');
            
            $table->decimal('amount', 10, 4)->unsigned()->nullable();
            $table->boolean('is_active')->default(0);
            $table->string('rejection_code',200)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_forced')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_discount_dispensary_sale', function(Blueprint $table){
			$table->dropForeign(['sale_id']);
			$table->dropForeign(['discount_id']);
		});
        Schema::drop('dispensary_discount_dispensary_sale');
    }

}