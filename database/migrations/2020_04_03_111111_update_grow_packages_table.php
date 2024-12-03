<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGrowPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grow_packages', function (Blueprint $table) {
            $table->decimal('received_price',10,4)->nullable()->after('received_from_facility_name');
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
           $table->dropColumn('received_price');
        });

    }
}
