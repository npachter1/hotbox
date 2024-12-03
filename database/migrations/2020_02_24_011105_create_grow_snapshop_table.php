<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowSnapshopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grow_snapshots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('location_id');
            $table->integer('plant_batch_count')->default(0);
            $table->integer('immature_plant_count')->default(0);
            $table->integer('clone_count')->default(0);
            $table->integer('seed_count')->default(0);
            $table->integer('mature_plant_count')->default(0);
            $table->integer('vegetative_plant_count')->default(0);
            $table->integer('flowering_plant_count')->default(0);
            $table->integer('harvest_count')->default(0);
            $table->decimal('harvest_total_current_weight',10,4)->default(0);
            $table->decimal('harvest_total_trim_weight',10,4)->default(0);
            $table->decimal('harvest_total_waste_weight',10,4)->default(0);
            $table->integer('package_count')->default(0);
            $table->decimal('package_total_weight')->default(0);

            $table->foreign('location_id')->references('id')->on('auth_locations');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grow_snapshots');
    }
}
