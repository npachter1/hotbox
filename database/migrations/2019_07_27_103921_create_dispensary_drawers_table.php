<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryDrawersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_drawers', function (Blueprint $table){
            $table->engine = 'InnoDB';
            
            $table->increments('id')->unsigned();
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');

			$table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('name',191)->index()->nullable();

			$table->decimal('opening_balance', 10, 4)->default(0.0000);
			$table->decimal('current_balance', 10, 4)->default(0.0000);
			$table->decimal('closing_balance', 10, 4)->nullable();
			$table->integer('total_sales')->default(0);
			$table->decimal('total_sale_price', 10, 4)->default(0.0000);
			$table->decimal('total_thc_equivalent_grams', 10, 4)->default(0.0000);
			
			$table->dateTime('closed_at')->nullable();
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
        
        Schema::table('dispensary_drawers', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['user_id']);
		});
        Schema::dropIfExists('dispensary_drawers');
        
    }
    
}