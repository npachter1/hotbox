<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThcCbdTerpColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispensary_products', function (Blueprint $table) {
            $table->decimal('thc_percentage',6, 3)->after('public_img')->nullable();
            $table->decimal('cbd_percentage',6, 3)->after('thc_percentage')->nullable();
            $table->decimal('terpene_percentage',6, 3)->after('cbd_percentage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_products', function (Blueprint $table) {
            $table->dropColumn('thc_percentage');
            $table->dropColumn('cbd_percentage');
            $table->dropColumn('terpene_percentage');
        });
    }
}
