<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryCategoriesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_categories', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            
            $table->integer('location_id');
			$table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
            $table->integer('metrc_category_id')->unsigned()->nullable();
            $table->text('name')->nullable();

            $table->string('equivalency_type',191)->index()->nullable()->default('misc');
			$table->boolean('contains_thc')->default(1);
			
			$table->mediumText('settings')->nullable();
			
            $table->text('notes')->nullable();
            $table->text('public_img')->nullable();
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
        
        Schema::table('dispensary_categories', function(Blueprint $table){
            $table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('dispensary_categories');
        
    }
    
}