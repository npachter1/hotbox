<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGrowTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::statement("ALTER TABLE `grow_transfers` CHANGE `status` `status` ENUM('intransit','confirmed','rejected','unpaid','unknown') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        
        Schema::table('grow_transfers', function (Blueprint $table) {
            $table->decimal('transfersale_fee',10,4)->nullable()->after('manifest_data');
            $table->decimal('transfersale_total',10,4)->nullable()->after('manifest_data');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grow_transfers', function (Blueprint $table) {
           $table->dropColumn('transfersale_fee');
           $table->dropColumn('transfersale_total');
        });

    }
}
