<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensarySalePaymentsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_sale_payments', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
			$table->integer('sale_id')->unsigned();
			$table->foreign('sale_id')->references('id')->on('dispensary_sales')->onDelete('cascade');

			$table->enum('payment_method', array('cash','credit','gift card','account'));
			$table->string('payment_method_unique_identifier', 191)->nullable();
			$table->decimal('amount', 10, 4)->unsigned();
            $table->decimal('amount_owed', 10, 4)->unsigned()->nullable();

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
        
        Schema::table('dispensary_sale_payments', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['sale_id']);
		});
        Schema::dropIfExists('dispensary_sale_payments');
        
    }
    
}