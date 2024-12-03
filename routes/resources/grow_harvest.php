<?php

/* grow/plants/harvests routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/harvests', 'HarvestController@index');
    Route::get('/harvests/batch', 'HarvestController@batch');
    Route::get('/harvests/export/{type}', 'HarvestController@exportGrid');
    
    Route::get('/harvests/{id}/export/{type}', 'HarvestController@exportView');
    Route::get('/harvests/{id}', 'HarvestController@show');
    Route::post('/harvests/removeWaste', 'HarvestController@removeWaste');
    Route::post('/harvests/finish', 'HarvestController@finish');
    Route::post('/harvests/unfinish', 'HarvestController@unfinish');
    Route::post('/harvests/createPackage', 'HarvestController@createPackage');
    Route::post('/harvests/createPackages', 'HarvestController@createPackages');
    Route::post('/harvests/{id}/addActivity', 'HarvestController@addActivity');
    Route::post('/harvests/{id}/addMaterial', 'HarvestController@addMaterial');

});