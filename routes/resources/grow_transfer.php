<?php

/* grow/plants/packages routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/transfers', 'TransferController@index');
    Route::get('/transfers/export/{type}', 'TransferController@exportGrid');
    Route::get('/transfers/{id}/resend', 'TransferController@resendTransferImport');
    Route::get('/transfers/{id}', 'TransferController@show');

    Route::post('/transfers/{id}/invoice/export','TransferController@generateInvoicePDF');
    Route::post('/transfers', 'TransferController@store');
    Route::put('/transfers/{id}/modify', 'TransferController@modify');
    Route::delete('/transfers/{id}', 'TransferController@cancel');

});