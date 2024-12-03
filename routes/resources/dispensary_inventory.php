<?php 

/* dispensary/products/inventory api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/inventories', 'InventoryController@index');
    Route::get('/inventories/batch', 'InventoryController@batch');
    Route::get('/inventories/export/{type}', 'InventoryController@exportGrid');
    Route::get('/inventories/{id}/export/{type}', 'InventoryController@exportView');
    Route::get('/inventories/{id}', 'InventoryController@show');
    Route::post('/inventories/batch/{type}', 'InventoryController@updateBatch');
    Route::post('/inventories', 'InventoryController@store');
    Route::put('/inventories/{id}', 'InventoryController@update');
    Route::delete('/inventories/{id}', 'InventoryController@destroy');
    Route::get('/inventories/label/schema','LabelPrinterController@getDymoLabelSchema');
    Route::post('/inventories/label/render','LabelPrinterController@render');
    
    Route::get('/inventoriesstrains', 'InventoryController@strainShow');
    Route::post('/inventoriesstrains', 'InventoryController@strainUpdate');

});