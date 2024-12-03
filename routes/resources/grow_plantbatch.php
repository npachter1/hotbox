<?php

/* grow/plants/plantbatches routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/plantbatches', 'PlantBatchController@index');
    Route::get('/plantbatches/export/{type}', 'PlantBatchController@exportGrid');
    Route::post('/plantbatches/splitBatch', 'PlantBatchController@splitBatch');
    Route::get('/plantbatches/{id}/export/{type}', 'PlantBatchController@exportView');
    Route::get('/plantbatches/{id}', 'PlantBatchController@show');
    Route::put('/plantbatches/{id}', 'PlantBatchController@update');
    Route::post('/plantbatches/createPackage', 'PlantBatchController@createPackage');
    Route::post('/plantbatches/{id}/destroy', 'PlantBatchController@destroyImmature');
    Route::post('/plantbatches/{id}/changeGrowthPhase', 'PlantBatchController@changeGrowthPhase');

});