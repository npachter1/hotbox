<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowStrainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_strains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('metrc_id')->unsigned()->nullable()->indexed();
            $table->string('name');
            $table->enum('testing_status', array('None', 'ThirdParty', 'InHouse'))->nullable(); // TODO Research possible values
            $table->integer('location_id');
            $table->decimal('thc_level', 10, 4)->nullable();
            $table->decimal('cbd_level', 10, 4)->nullable();
            $table->decimal('indica_percentage', 10, 4);  // sum of indica & sativa percentage will be 100
            $table->decimal('sativa_percentage', 10, 4);  // sum of indica & sativa percentage will be 100
            $table->decimal('breakdown_thc', 10, 4)->nullable();
            $table->decimal('breakdown_thca', 10, 4)->nullable();
            $table->decimal('breakdown_cbd', 10, 4)->nullable();
            $table->decimal('breakdown_cbda', 10, 4)->nullable();
            $table->decimal('breakdown_cbn', 10, 4)->nullable();
            $table->decimal('breakdown_cbg', 10, 4)->nullable();
            $table->decimal('breakdown_thcv', 10, 4)->nullable();
            $table->decimal('breakdown_cbc', 10, 4)->nullable();
            $table->decimal('breakdown_cbl', 10, 4)->nullable();
            $table->enum('metrc_status', array('synced', 'updating', 'creating', 'deleting', 'error'))->nullable();
            $table->bigInteger('metrc_create_transaction_id')->unsigned()->nullable();
            $table->bigInteger('metrc_update_transaction_id')->unsigned()->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('auth_locations');
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
        Schema::dropIfExists('grow_strains');
    }
}
