<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensarySalesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_sales', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			$table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('customer_id')->unsigned();
			$table->foreign('customer_id')->references('id')->on('dispensary_customers')->onDelete('cascade');
			$table->integer('drawer_id')->unsigned();
			$table->foreign('drawer_id')->references('id')->on('dispensary_drawers')->onDelete('cascade');
			
			$table->string('order_number', 191)->index()->nullable();
			$table->decimal('price', 10, 4)->nullable();
			$table->decimal('discount', 10, 4)->nullable();
			$table->decimal('tax', 10, 4)->nullable();
			$table->decimal('sale_price', 10, 4)->nullable();
			$table->decimal('thc_equivalent_grams', 10, 4)->nullable();
			$table->string('discount_code',191)->index()->nullable();
			$table->text('discount_descriptor')->nullable();
			$table->enum('status', array('settled','pending','voided','refunded'))->nullable()->default('settled');
            $table->enum('metrc_status',['confirmed','unconfirmed','exception','new'])->nullable();
			$table->text('metrc_exception')->nullable();
			$table->string('metrc_receipt', 191)->nullable();
           
            $table->dateTime('migrated_at')->nullable();
            $table->dateTime('archived_at')->nullable();
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
        
        Schema::table('dispensary_sales', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['user_id']);
			$table->dropForeign(['customer_id']);
			$table->dropForeign(['drawer_id']);
		});
        Schema::dropIfExists('dispensary_sales');
        
    }
    
}