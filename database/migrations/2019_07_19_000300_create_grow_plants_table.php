<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowPlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_plants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->string('label')->nullable();
            $table->enum('growth_phase', array('Young', 'Vegetative', 'Flowering'));
            $table->enum('state', array('Tracked', 'Harvested', 'Destroyed'));  
            $table->integer('plant_batch_id')->unsigned()->nullable();
            $table->integer('strain_id')->unsigned()->nullable();
            $table->integer('room_id')->unsigned()->nullable();
            $table->integer('location_id');
            $table->string('patient_license_number')->nullable();
            $table->integer('harvest_id')->unsigned()->nullable();
            $table->enum('harvested_unit_of_weight', array('mg', 'g', 'oz', 'kg', 'lb'));
            $table->decimal('harvested_wet_weight', 10, 4)->unsigned()->nullable();
            $table->boolean('is_on_hold')->default(false);
            $table->dateTime('planted_at')->nullable();
            $table->dateTime('vegetative_at')->nullable(); // will not be earlier than planted_at
            $table->dateTime('flowering_at')->nullable();  // will not be earlier than vegetative_at
            $table->dateTime('harvested_at')->nullable();  // will not be earlier than flowering_at
            $table->dateTime('destroyed_at')->nullable();  // will not be earlier than planted_at
            $table->dateTime('last_modified')->nullable();
            $table->string('destroyed_note')->nullable();
            $table->uuid('destroyed_by')->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->bigInteger('metrc_create_transaction_id')->unsigned()->nullable();
            $table->bigInteger('metrc_update_transaction_id')->unsigned()->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('auth_locations');
            $table->foreign('plant_batch_id')->references('id')->on('grow_plant_batches');
            $table->foreign('strain_id')->references('id')->on('grow_strains');
            $table->foreign('room_id')->references('id')->on('grow_rooms');
            $table->foreign('harvest_id')->references('id')->on('grow_harvests');
            $table->foreign('metrc_create_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('metrc_update_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('destroyed_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
                       
            $table->unique('metrc_id');
            $table->unique('label');
        });
    
        Schema::table('grow_plant_batches', function (Blueprint $plantBatches) {
            $plantBatches->foreign('source_plant_id')->references('id')->on('grow_plants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grow_plant_batches', function (Blueprint $plantBatches) {
           $plantBatches->dropForeign('grow_plant_batches_source_plant_id_foreign');
        });
        Schema::dropIfExists('grow_plants');
    }
}
