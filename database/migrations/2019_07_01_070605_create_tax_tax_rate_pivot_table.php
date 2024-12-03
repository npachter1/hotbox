<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxTaxRatePivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('dispensary_tax_dispensary_tax_rate', function (Blueprint $table) {

            $table->integer('tax_id')->unsigned();
            $table->foreign('tax_id')->references('id')->on('dispensary_taxes')->onDelete('cascade');
            $table->integer('tax_rate_id')->unsigned();
            $table->foreign('tax_rate_id')->references('id')->on('dispensary_tax_rates')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_tax_dispensary_tax_rate', function(Blueprint $table){
			$table->dropForeign(['tax_id']);
			$table->dropForeign(['tax_rate_id']);
		});
        Schema::drop('dispensary_tax_dispensary_tax_rate');
    }

}