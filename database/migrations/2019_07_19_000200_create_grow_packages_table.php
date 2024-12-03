<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->string('label')->nullable();
            $table->enum('package_type', array('Product', 'ImmaturePlant', 'VegetativePlant'));
            $table->string('source_harvest_names')->nullable();
            $table->integer('room_id')->unsigned()->nullable();
            $table->integer('location_id');
            $table->decimal('quantity', 10, 4);
            $table->enum('unit_of_measure', array('mg','g','oz','kg','lb','ea'))->nullable();
            $table->string('patient_license_number')->nullable();
            $table->integer('product_id')->unsigned();
            $table->dateTime('packaged_at')->nullable();
            $table->boolean('is_production_batch')->default(false);
            $table->string('production_batch_number')->nullable();
            $table->boolean('is_testing_sample')->default(false);
            $table->boolean('is_process_validation_testing_sample')->default(false);
            $table->boolean('contains_remediated_product')->default(false);
            $table->dateTime('remediation_at')->nullable();
            $table->string('received_from_manifest_number')->nullable();
            $table->string('received_from_facility_license_number')->nullable();
            $table->string('received_from_facility_name')->nullable();
            $table->dateTime('received_at')->nullable();  // will not be earlier than packaged_at
            $table->string('lab_testing_state')->nullable();
            $table->string('lab_testing_state_date')->nullable();
            $table->boolean('is_on_hold')->default(false);
            $table->dateTime('archived_at')->nullable();  // will not be earlier than finished_at
            $table->dateTime('finished_at')->nullable();  // will not be earlier than packaged_at
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
            $table->foreign('product_id')->references('id')->on('grow_items');
            $table->foreign('metrc_create_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('metrc_update_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
                       
            $table->unique('metrc_id');
            $table->unique('label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grow_packages');
    }
}
