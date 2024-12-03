<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGrowPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grow_packages', function (Blueprint $table) {
            $table->integer('plant_batch_id')->unsigned()->nullable()->after('source_harvest_names');

            $table->foreign('plant_batch_id')->references('id')->on('grow_plant_batches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grow_packages', function (Blueprint $table) {
            $table->dropColumn('plant_batch_id');
        });
    }
}
