<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('users', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->integer('location_id')->index();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->enum('type',['admin','staff','contractor'])->default('staff');
            $table->char('pincode', 8)->nullable()->unique('pincode');
            $table->string('name',191)->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_unique_id')->nullable();
            $table->enum('status', ['pending_activation','activated','denied','held','archived'])->nullable();
            $table->boolean('is_prime')->default(0);
            $table->string('activation_token', 64)->nullable();
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->mediumText('settings')->nullable();
            
            $table->dateTime('archived_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
        
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        
    }
}
