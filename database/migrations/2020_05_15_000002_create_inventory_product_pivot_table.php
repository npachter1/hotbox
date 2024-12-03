<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInventoryProductPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('disp_inventory_disp_product', function (Blueprint $table) { // this is to assign items to a product for type of "grouping"

            $table->integer('disp_inventory_id')->unsigned();
            $table->foreign('disp_inventory_id')->references('id')->on('dispensary_inventories')->onDelete('cascade');
            $table->integer('disp_product_id')->unsigned();
            $table->foreign('disp_product_id')->references('id')->on('dispensary_products')->onDelete('cascade');
            
            $table->float('quantity', 12, 6)->nullable()->default(0.000000);
            $table->enum('policy',['sellout','restock','preorder','none'])->nullable()->default('restock');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disp_inventory_disp_product', function(Blueprint $table){
			$table->dropForeign(['disp_inventory_id']);
			$table->dropForeign(['disp_product_id']);
		});
        Schema::drop('disp_inventory_disp_product');
    }

}