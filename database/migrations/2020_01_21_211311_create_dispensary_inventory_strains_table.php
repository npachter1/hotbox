<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryInventoryStrainsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_inventory_strains', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
			
			$table->string('name',191)->nullable()->index();					// indexed strain name
			$table->text('notes')->nullable();									// Any notes for the strain
			$table->integer('inventory_count')->default(0);
			$table->decimal('items_count',10,6)->default(0);
           
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
        
        Schema::table('dispensary_inventory_strains', function(Blueprint $table){

		});
        Schema::dropIfExists('dispensary_inventory_strains');
        
    }
    
}