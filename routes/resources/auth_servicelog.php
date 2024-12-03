<?php 

/* superadmin suites api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Auth', 'prefix' => 'v1/admin/auth', 'as' => 'v1.admin.auth.'], function (){

    Route::get('/servicelogs', 'ServicelogController@index');
    Route::get('/servicelogs/batch', 'ServicelogController@batch');
    Route::get('/servicelogs/export/{type}', 'ServicelogController@exportGrid');
    Route::get('/servicelogs/{id}/export/{type}', 'ServicelogController@exportView');
    Route::get('/servicelogs/{id}', 'ServicelogController@show');
    Route::post('/servicelogs/batch/{type}', 'ServicelogController@updateBatch');
    Route::post('/servicelogs', 'ServicelogController@store');
    Route::put('/servicelogs/{id}', 'ServicelogController@update');
    Route::delete('/servicelogs/{id}', 'ServicelogController@destroy');

});