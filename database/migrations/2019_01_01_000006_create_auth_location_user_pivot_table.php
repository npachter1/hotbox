<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateAuthLocationUserPivotTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up(){
        
        Schema::create('auth_location_user', function (Blueprint $table) {

            $table->integer('auth_location_id');
            $table->foreign('auth_location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
        
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('auth_location_user', function(Blueprint $table){
			$table->dropForeign(['auth_location_id']);
			$table->dropForeign(['user_id']);
		});
        Schema::drop('auth_location_user');
        
    }

    
}
