<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateMetrcDispensaryTransfersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('metrc_dispensary_transfers', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->integer('metrc_id')->unsigned()->nullable()->index();
            $table->string('manifest_id',191)->index()->nullable();
            $table->string('vendor_ref',191)->index()->nullable();
            $table->string('vendor_licensenum',191)->index()->nullable();
            $table->mediumText('manifest_data')->nullable();
       
            $table->enum('status', array('intransit', 'confirmed', 'rejected', 'unknown'))->index()->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();

			$table->dateTime('received_at')->nullable();
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
        
        Schema::table('metrc_dispensary_transfers', function(Blueprint $table){
			$table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('metrc_dispensary_transfers');
        
    }
    
}