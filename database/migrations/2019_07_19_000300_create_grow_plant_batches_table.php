<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowPlantBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_plant_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->enum('type', array('Seed','Clone'));
            $table->integer('location_id');
            $table->integer('room_id')->unsigned()->nullable();
            $table->integer('strain_id')->unsigned()->nullable();
            $table->string('patient_license_number')->nullable();
            $table->integer('count')->unsigned();
            $table->integer('live_count')->unsigned();
            $table->integer('packaged_count')->unsigned();
            $table->integer('harvested_count')->unsigned();
            $table->integer('destroyed_count')->unsigned();
            $table->integer('source_package_id')->unsigned()->nullable();
            $table->integer('source_plant_id')->unsigned()->nullable();
            $table->dateTime('last_modified')->nullable();
            $table->dateTime('planted_at')->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->bigInteger('metrc_create_transaction_id')->unsigned()->nullable();
            $table->bigInteger('metrc_update_transaction_id')->unsigned()->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('auth_locations');
            $table->foreign('room_id')->references('id')->on('grow_rooms');
            $table->foreign('strain_id')->references('id')->on('grow_strains');
            $table->foreign('source_package_id')->references('id')->on('grow_packages');
            //$table->foreign('source_plant_id')->references('id')->on('grow_plants');
            $table->foreign('metrc_create_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('metrc_update_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
                       
            $table->unique('metrc_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grow_plant_batches');
    }
}
