<?php

/* grow/plants/packages routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/packages', 'PackageController@index');
    Route::get('/packages/batch', 'PackageController@batch');
    Route::get('/packages/export/{type}', 'PackageController@exportGrid');
    
    Route::get('/packages/{id}/export/{type}', 'PackageController@exportView');
    Route::get('/packages/{id}', 'PackageController@show');
    Route::put('/packages/{id}', 'PackageController@update');
    Route::post('/packages/changeItem', 'PackageController@changeItem');
    Route::post('/packages/adjust', 'PackageController@adjust');
    Route::post('/packages/remediate', 'PackageController@remediate');
    Route::post('/packages/createPackage', 'PackageController@createPackage');
    Route::post('/packages/createPackages', 'PackageController@createPackages');

});