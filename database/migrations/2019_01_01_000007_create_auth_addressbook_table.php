<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateAuthAddressbookTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('auth_addressbook', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('location_id')->nullable()->index();                // addresses arent scoped to location, but, this will help decifer from which location an address was added.
            $table->integer('third_party_id')->nullable();                      // for future use
            $table->enum('type', ['vendor','consumer','location','retailer','misc'])->index()->nullable();
            $table->string('name', 191)->index()->nullable();
            $table->string('address1', 191)->nullable();
            $table->string('address2', 191)->nullable();
            $table->string('city', 191)->index()->nullable();
            $table->string('region', 191)->index()->nullable();
            $table->string('country', 191)->index()->nullable();
            $table->string('county', 191)->index()->nullable();
            $table->string('zip', 191)->index()->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('email',191)->nullable();
            $table->text('contact_notes')->nullable();
            $table->text('licensenum')->nullable();                             // for vendors
            $table->text('website')->nullable();
            $table->string('industry',100)->nullable()->index();                // industry type for vendors/3rd parties selections
            $table->dateTime('archived_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();
            
            $table->index(['created_at','location_id']);
            $table->index(['region','location_id','created_at']);
            $table->index(['country','location_id','created_at']);

            
        });
        
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        
        Schema::dropIfExists('auth_addressbook');
        
    }
    
    
}
