<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class UpdateDispensaryProductsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispensary_products', function (Blueprint $table) {
            $table->enum('type',['single','grouping','kit','misc'])->index()->default('single')->after('inv_meta');
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
           $table->dropColumn('type');
        });
    }
    
}