<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDispensaryReceivingsTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        
        Schema::create('dispensary_receivings', function (Blueprint $table){
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('auth_locations')->onDelete('cascade');
            
			$table->integer('addressbook_id')->unsigned()->nullable()->index(); // vendor addess of who is sending the articles to be received
			$table->string('transfer_id',191)->index()->nullable();				// transfer number / manifest id from a metrc transfer
			$table->text('invoice_refs')->nullable();						    // invoice ref(s) to help with metrc mapping
			$table->text('po_number')->nullable();								// number of PurchaseOrder, or other metrc transfer id
			$table->enum('type', ['metrc','purchaseorder','grow','cbd','custom'])->nullable();// type of receiving file, metrc = metrc transfer, purchaseorder for all non-cannabis receivings
			$table->enum('status', ['created','sent','held','confirmed','rejected','received','admin'])->nullable(); // current stte of the receiving file
			$table->integer('articles_sent')->nullable();						// # of packages/or itemlines in file sent by vendor (for reporting purposes)
			$table->integer('articles_received')->nullable();					// # of packages/or itemlines in file actually receivedr (for exceptions reporting / logging purposes)
			$table->decimal('articles_value',10,4)->nullable();					// total value of items being received
			$table->mediumText('items')->nullable();							// json (casted to stdClass) cache of items to be received
			$table->dateTime('sent_at')->nullable();							// date file was sent to a vendor to fulfill
			$table->dateTime('confirmed_at')->nullable();						// date metrc transfer was confirmed (received in metrc), or vendor accepts if non thc
			$table->dateTime('received_at')->nullable();						// date items from first article(s) were recieved into inventory
            
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
        
        Schema::table('dispensary_receivings', function(Blueprint $table){
			$table->dropForeign(['location_id']);
		});
        Schema::dropIfExists('dispensary_receivings');
        
    }
    
}