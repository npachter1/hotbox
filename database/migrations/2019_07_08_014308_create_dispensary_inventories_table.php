<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryInventoriesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_inventories', function (Blueprint $table){
            $table->engine = 'InnoDB';
			$table->increments('id')->unisgned();
			$table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('RESTRICT');
			$table->integer('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('dispensary_products')->onDelete('RESTRICT');
			$table->integer('addressbook_id')->unsigned()->index();
			    //$table->foreign('addressbook_id')->references('id')->on('auth_addressbook')->onDelete('RESTRICT');
			$table->integer('receiving_id')->unsigned()->index();
			    //$table->foreign('receiving_id')->references('id')->on('dispensary_receivings')->onDelete('RESTRICT');
			$table->integer('priceset_id')->nullable()->unsigned()->index();
			$table->string('metrc_product_id', 191)->nullable()->index();
			$table->string('metrc_tag', 191)->nullable()->index();
			$table->text('metrc_parent_tags')->nullable();
			$table->string('grow_product_id',191)->nullable();
			$table->string('room_ref', 191)->nullable()->index();
			$table->string('item_barcode', 191)->nullable()->index();
			$table->text('item_batch')->nullable();
			$table->string('item_strain', 191)->nullable();
			$table->text('item_name')->nullable();
			$table->text('item_notes')->nullable();
			$table->float('quantity_received', 12, 6);
			$table->float('quantity_sold', 12, 6)->nullable()->default(0.000000);
			$table->float('quantity_pending', 12, 6)->nullable()->default(0.000000);
			$table->float('quantity_on_hand', 12, 6)->nullable()->default(0.000000);
			$table->float('quantity_adjust', 12, 6)->nullable()->default(0.000000);
			$table->decimal('quantity_requested',12,6)->nullable();
			$table->enum('unit_of_measure', array('mg','g','oz','kg','lb','ea'))->nullable();
			$table->float('amount_unit', 12, 6)->nullable()->default(1.000000);	// amount of grams in 1 unit - for prepackaged thc regulated in grams
			$table->float('weight_potency', 12, 6)->nullable()->default(1.000000);	// potency weight (in grams) of 1 unit
			$table->decimal('cost_unit', 10, 4)->nullable()->default(0.0100);
			$table->decimal('retail_unit', 10, 4)->nullable()->default(0.0100);
			$table->dateTime('expires_at')->nullable();
			$table->dateTime('audited_at')->nullable();
			$table->uuid('audited_by')->index()->nullable();
			$table->dateTime('archived_at')->nullable();
            $table->enum('metrc_status',['confirmed','unconfirmed','exception','new'])->nullable();
			$table->text('metrc_exception')->nullable();
			

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
        
        Schema::table('dispensary_inventories', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['product_id']);
			//$table->dropForeign(['addressbook_id']);
			//$table->dropForeign(['receiving_id']);
		});
        Schema::dropIfExists('dispensary_inventories');
        
    }
    
}