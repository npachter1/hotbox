<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryTaxRatesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_tax_rates', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
			$table->string('name', 191)->index()->nullable();
			$table->text('description')->nullable();
            $table->enum('tax_rate_level', array('federal', 'state', 'county', 'city', 'local', 'excise'))->nullable()->index();
			$table->char('state_code', 2)->nullable()->index();
			$table->string('county_name')->nullable()->index();
			$table->string('city_name')->nullable()->index();
			$table->char('zipcode', 10)->nullable()->index();
			$table->decimal('rate_percent', 5)->nullable()->default(0.01);

            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');            
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
            
            
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
        
        Schema::table('dispensary_tax_rates', function(Blueprint $table){
			$table->dropForeign(['created_by']);
			$table->dropForeign(['updated_by']);
			$table->dropForeign(['deleted_by']);
		});
        Schema::dropIfExists('dispensary_tax_rates');
        
    }
    
}