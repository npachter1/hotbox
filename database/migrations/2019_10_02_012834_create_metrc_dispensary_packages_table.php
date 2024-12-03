<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateMetrcDispensaryPackagesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('metrc_dispensary_packages', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->integer('metrc_id')->unsigned()->nullable()->index();
            $table->integer('metrc_product_id')->unsigned()->nullable()->index();
            $table->integer('metrc_category_id')->unsigned()->nullable()->index();
            $table->integer('grow_product_id')->unsigned()->nullable()->index();
            $table->string('label',191)->index()->nullable();
            $table->string('name',191)->index()->nullable();
            $table->string('manifest_id',191)->index()->nullable();
            
            $table->enum('status', array('intransit', 'confirmed', 'rejected','held','unknown'))->index()->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->decimal('quantity', 10, 4)->default(0);
            $table->string('uom',191)->nullable();
            $table->integer('product_id')->unsigned()->nullable()->index();
            $table->integer('vendor_id')->unsigned()->nullable()->index();
            $table->integer('category_id')->unsigned()->nullable()->index();
            $table->text('product_search')->nullable();
            $table->decimal('weight_potency',10,6)->nullable();
            
            $table->mediumText('package_data')->nullable();
            
            $table->dateTime('received_at')->nullable();  // will not be earlier than packaged_at
            $table->dateTime('archived_at')->nullable();  // will not be earlier than finished_at

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
        
        Schema::table('metrc_dispensary_packages', function(Blueprint $table){
			$table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('metrc_dispensary_packages');
        
    }
    
}