<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensarySaleItemsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_sale_items', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			
			$table->integer('inventory_id')->unsigned();
			$table->foreign('inventory_id')->references('id')->on('dispensary_inventories')->onDelete('cascade');			
			$table->integer('sale_id')->unsigned();
			$table->foreign('sale_id')->references('id')->on('dispensary_sales')->onDelete('cascade');
			$table->integer('tax_id')->nullable()->index();
			//$table->foreign('tax_id')->references('id')->on('dispensary_taxes')->onUpdate('RESTRICT')->onDelete('RESTRICT');

			$table->decimal('quantity', 10, 4)->default(0);
			$table->decimal('price', 10, 4)->nullable();
			$table->decimal('discount', 10, 4)->nullable();
			$table->decimal('tax', 10, 4)->nullable();
			$table->decimal('sale_price', 10, 4)->nullable();
			$table->decimal('thc_equivalent_grams', 10, 4)->nullable();
			$table->decimal('quantity_priced_at', 10, 4)->default(0);
			$table->boolean('is_confirmed')->default(0);
			$table->string('discount_rule_ids')->nullable();
			$table->string('discount_ref')->nullable();
			$table->text('discount_descriptor')->nullable();

			$table->dateTime('migrated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('dispensary_sale_items', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['inventory_id']);
			$table->dropForeign(['sale_id']);
			//$table->dropForeign(['tax_id']);
		});
        Schema::dropIfExists('dispensary_sale_items');
        
    }
    
}