<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispensary_customers', function (Blueprint $table) {
            $table->string('contact_name')->after('alias')->nullable();
            $table->string('receiving_party_name')->after('contact_name')->nullable();
            $table->string('employee_position')->after('receiving_party_name')->nullable();
            $table->string('tax_exempt_number')->after('employee_position')->nullable();
            $table->enum('license_type',['grow','processor','dispensary','patient'])->after('tax_exempt_number')->nullable();
            $table->string('online_login_email')->unique()->nullable()->after('license_type');
            $table->string('password')->nullable()->after('online_login_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensary_customers', function (Blueprint $table) {
            $table->dropColumn('contact_name');
            $table->dropColumn('receiving_party_name');
            $table->dropColumn('employee_position');
            $table->dropColumn('tax_exempt_number');
            $table->dropColumn('license_type');
            $table->dropColumn('online_login_email');
            $table->dropColumn('password');
        });
    }
}
