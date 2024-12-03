<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateAuthServicelogsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('auth_servicelogs', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('type',191)->nullable();
            
            $table->enum('method',['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])->nullable();
            $table->string('base_url')->nullable();
            $table->string('processed_url')->nullable();
            $table->string('path')->nullable();
            $table->text('parameters')->nullable();
            $table->unsignedInteger('http_code')->nullable();
            $table->mediumText('response')->nullable();
            $table->unsignedInteger('total_time')->nullable();
            $table->text('error')->nullable();
            $table->text('exception')->nullable();
            $table->integer('location_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('location_id')->references('id')->on('auth_locations');
            $table->foreign('created_by')->references('id')->on('users');
            
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::table('auth_servicelogs', function(Blueprint $table){
			$table->dropForeign(['location_id']);
			$table->dropForeign(['created_by']);
		});
        Schema::dropIfExists('auth_servicelogs');
        
    }
    
}