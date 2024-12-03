<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowHarvestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_harvests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->string('name')->nullable();
            $table->integer('location_id');
            $table->enum('harvest_type', array('Product', 'WholePlant'));
            $table->integer('source_strain_count')->unsigned();
            $table->integer('drying_room_id')->unsigned();
            $table->string('patient_license_number')->nullable();
            $table->decimal('current_weight', 10, 4)->default(0.0000);  // will not be more than total_wet_weight
            $table->decimal('total_waste_weight', 10, 4)->default(0.0000);  // will not be more than total_wet_weight
            $table->decimal('total_trim_weight', 10, 4)->default(0.0000);  // will not be more than total_wet_weight
            $table->integer('plant_count')->unsigned()->default(0);
            $table->decimal('total_wet_weight', 10, 4)->default(0.0000);
            $table->decimal('total_restored_weight', 10, 4)->default(0.0000);
            $table->decimal('package_count', 10, 4)->default(0.0000);
            $table->decimal('total_packaged_weight', 10, 4)->default(0.0000);  // will not be more than total_wet_weight
            $table->enum('unit_of_weight', array('mg','g','oz','kg','lb'))->nullable();
            $table->string('lab_testing_state')->nullable();
            $table->string('lab_testing_state_date')->nullable();
            $table->boolean('is_on_hold')->default(false);
            $table->dateTime('harvest_start_at');
            $table->dateTime('finished_at')->nullable();  // will not be earlier than harvested_start_at
            $table->dateTime('archived_at')->nullable();  // will not be earlier than finished_at
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->bigInteger('metrc_create_transaction_id')->unsigned()->nullable();
            $table->bigInteger('metrc_update_transaction_id')->unsigned()->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('auth_locations');
            $table->foreign('drying_room_id')->references('id')->on('grow_rooms');
            $table->foreign('metrc_create_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('metrc_update_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
                       
            $table->unique(['location_id', 'name']);
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
        Schema::dropIfExists('grow_harvests');
    }
}
