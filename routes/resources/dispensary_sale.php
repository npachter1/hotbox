<?php 

/* dispensary/pos/sales api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/sales', 'SaleController@index');
    Route::get('/sales/batch', 'SaleController@batch');
    Route::get('/sales/export/{type}', 'SaleController@exportGrid');
    Route::get('/sales/{id}/void', 'SaleController@voidOrder');
    Route::post('/sales/{id}/return', 'SaleController@returnOrder');
    Route::get('/sales/{id}/export/{type}', 'ReceiptPrinterController@generateReceiptPDF');
    Route::get('/sales/{id}', 'SaleController@show');
    Route::get('/sales/{id}/receipt/file', 'ReceiptPrinterController@generateReceipt');
    Route::get('/sales/{id}/receipt/html', 'ReceiptPrinterController@generateReceiptHTML');
    Route::get('/sales/{id}/receipt/email', 'ReceiptPrinterController@generateReceiptEmail');
    Route::post('/sales/batch/{type}', 'SaleController@updateBatch');
    Route::post('/sales/{id}/payment', 'SaleController@payOrder');
    
    Route::post('/sales/{id}/account', 'SaleController@payAccount');
    Route::post('/sales', 'SaleController@store');
    Route::put('/sales/{id}', 'SaleController@update');
    Route::delete('/sales/{id}', 'SaleController@destroy');

});