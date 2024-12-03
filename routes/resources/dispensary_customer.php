
<?php

/* dispensary/administration/customer api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/customers', 'CustomerController@index');
    Route::post('/customers/login', 'CustomerController@login');
    Route::get('/customers/batch', 'CustomerController@batch');
    Route::get('/customers/export/{type}', 'CustomerController@exportGrid');
    Route::get('/customers/smurfname', 'CustomerController@getSmurfName');
    Route::get('/customers/{id}/export/{type}', 'CustomerController@exportView');
    Route::get('/customers/{id}', 'CustomerController@show');
    Route::post('/customers/batch/{type}', 'CustomerController@updateBatch');
    Route::post('/customers', 'CustomerController@store');
    Route::put('/customers/{id}', 'CustomerController@update');
    Route::delete('/customers/{id}', 'CustomerController@destroy');


    Route::get('/customersqueue', 'CustomerController@queueIndex');
    Route::get('/customersqueue/{type}/{id}', 'CustomerController@queueUpdate');
    Route::delete('/customersqueue', 'CustomerController@queueClear');

});
