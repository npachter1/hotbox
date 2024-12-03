<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterDispensarySalesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::table('dispensary_sales', function (Blueprint $table){
 			$table->dateTime('settled_at')->nullable();                         // record last settlement (transaction) date of sale
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        

    }
    
}