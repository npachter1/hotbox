<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUntrackedVegetativeStateToPlantBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grow_plant_batches', function (Blueprint $table) {
            DB::statement("ALTER TABLE grow_plant_batches MODIFY COLUMN type enum('Seed','Clone','Untracked Vegetative') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grow_plant_batches', function (Blueprint $table) {
            //
        });
    }
}
