<?php 

/* dispensary/products/receiving api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/receivings', 'ReceivingController@index');
    Route::get('/receivings/export/{type}', 'ReceivingController@exportGrid');
    
    Route::get('/receivings/transfers/import/{id}', 'ReceivingController@importTransfer');
    Route::get('/receivings/transfers', 'ReceivingController@transfersIndex');
    Route::get('/receivings/syncTransfers', 'ReceivingController@syncMetrcTransfers');

    Route::get('/receivings/packages', 'ReceivingController@packagesIndex');
    Route::get('/receivings/syncPackages', 'ReceivingController@syncMetrcPackages');

    
    Route::get('/receivings/{id}/export/{type}', 'ReceivingController@exportView');
    Route::get('/receivings/{id}', 'ReceivingController@show');
    Route::post('/receivings', 'ReceivingController@store');
    Route::post('/receivings/generate', 'ReceivingController@autoGenerate');
    Route::put('/receivings/{id}', 'ReceivingController@update');
    Route::delete('/receivings/{id}', 'ReceivingController@destroy');

});