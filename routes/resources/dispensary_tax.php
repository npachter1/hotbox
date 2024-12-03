<?php 

/* dispensary/administration/tax api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/taxes', 'TaxController@index');
    Route::get('/taxes/export/{type}', 'TaxController@exportGrid');
    Route::get('/taxes/{id}/export/{type}', 'TaxController@exportView');
    Route::get('/taxes/{id}', 'TaxController@show');
    Route::post('/taxes', 'TaxController@store');
    Route::put('/taxes/{id}', 'TaxController@update');
    Route::delete('/taxes/{id}', 'TaxController@destroy');
    
    Route::get('/taxrates/{id}', 'TaxController@rateShow');
    Route::post('/taxrates', 'TaxController@rateCreate');
    Route::put('/taxrates/{id}', 'TaxController@rateUpdate');

});