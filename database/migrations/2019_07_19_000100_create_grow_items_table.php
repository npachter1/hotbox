<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->integer('location_id');
            $table->string('name')->nullable();
            $table->integer('metrc_category_id')->unsigned();
            $table->string('category_type',191)->index()->nullable();
            $table->enum('unit_of_measure', array('mg', 'g', 'oz', 'kg', 'lb', 'ea'))->nullable();
            $table->integer('strain_id')->unsigned()->nullable();
            $table->string('item_brand')->nullable();
            $table->string('administration_method')->nullable();
            $table->decimal('unit_cbd_percent')->nullable();
            $table->decimal('unit_cbd_content')->nullable();
            $table->enum('unit_cbd_content_unit_of_measure', array('mg', 'g', 'oz', 'kg', 'lb'))->nullable();
            $table->decimal('unit_thc_percent')->nullable();
            $table->decimal('unit_thc_content')->nullable();
            $table->enum('unit_thc_content_unit_of_measure', array('mg', 'g', 'oz', 'kg', 'lb'))->nullable();
            $table->decimal('unit_volume')->nullable();
            $table->enum('unit_volume_unit_of_measure', array('mg', 'g', 'oz', 'kg', 'lb'))->nullable();
            $table->decimal('unit_weight')->nullable();
            $table->enum('unit_weight_unit_of_measure', array('mg', 'g', 'oz', 'kg', 'lb'))->nullable();
            $table->decimal('serving_size')->nullable();
            $table->integer('supply_duration_days')->nullable();
            $table->string('ingredients')->nullable();
            $table->boolean('is_used')->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->bigInteger('metrc_create_transaction_id')->unsigned()->nullable();
            $table->bigInteger('metrc_update_transaction_id')->unsigned()->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('auth_locations');
            $table->foreign('strain_id')->references('id')->on('grow_strains');
            //$table->foreign('metrc_category_id')->references('id')->on('metrc_dispensary_categories');
            $table->foreign('metrc_create_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('metrc_update_transaction_id')->references('id')->on('auth_servicelogs');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
                       
            $table->unique('metrc_id');
            $table->unique(['location_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grow_items');
    }
}
