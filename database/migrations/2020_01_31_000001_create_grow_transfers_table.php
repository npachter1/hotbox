<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateGrowTransfersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('grow_transfers', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->bigInteger('dispensary_receiving_id')->unsigned()->nullable()->index();
            $table->integer('addressbook_id')->unsigned()->nullable()->index(); // vendor addess of who is sending the articles to be received
            $table->enum('type', array('metrc', 'internal', 'external', 'unknown'))->index()->nullable();
            
            $table->integer('metrc_id')->unsigned()->nullable()->index();
            $table->string('manifest_number',191)->index()->nullable();
            $table->string('transporter_name',191)->index()->nullable();
            $table->string('transporter_licensenum',191)->index()->nullable();
            $table->mediumText('manifest_data')->nullable();
       
            $table->enum('status', array('intransit', 'confirmed', 'rejected', 'unknown'))->index()->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();

            $table->uuid('created_by')->nullable();
			$table->dateTime('received_at')->nullable();
            $table->dateTime('archived_at')->nullable();  // will not be earlier than finished_at
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
        
        
        Schema::table('grow_packages', function($table) {
            $table->integer('transfer_id')->unsigned()->nullable()->indexed();
        });

    }
    
    

    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('grow_transfers', function(Blueprint $table){
			$table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('grow_transfers');
        
        Schema::table('grow_packages', function($table) {
            $table->dropColumn('transfer_id');
        });
        
    }
    
}