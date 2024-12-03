<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryProductsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_products', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('dispensary_categories')->onDelete('cascade');

            $table->text('name')->nullable();
            $table->string('sku',191)->index()->nullable();
            $table->text('description')->nullable();
            $table->text('inv_meta')->nullable();
            $table->enum('nature_type', array('recreational','medical','noncannabis','misc'))->nullable();
            $table->text('public_img')->nullable();
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
        
        Schema::table('dispensary_products', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['category_id']);
		});
        Schema::dropIfExists('dispensary_products');
        
    }
    
}