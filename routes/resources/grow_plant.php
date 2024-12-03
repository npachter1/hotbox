<?php

/* grow/plants/plants routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/plants', 'PlantController@index');
    Route::get('/plants/batch', 'PlantController@batch');
    Route::get('/plants/export/{type}', 'PlantController@exportGrid');
    
    Route::get('/plants/{id}/export/{type}', 'PlantController@exportView');
    Route::get('/plants/{id}', 'PlantController@show');
    Route::put('/plants/{id}', 'PlantController@update');
    Route::post('/plants/createPlantings', 'PlantController@createPlantings');
    Route::post('/plants/destroy', 'PlantController@destroy');
    Route::post('/plants/movePlants', 'PlantController@movePlants');
    Route::post('/plants/changeGrowthPhase', 'PlantController@changeGrowthPhase');
    Route::post('/plants/manicure', 'PlantController@manicure');
    Route::post('/plants/harvest', 'PlantController@harvest');
    Route::post('/plants/addMaterial', 'PlantController@addMaterial');
    Route::post('/plants/addActivity', 'PlantController@addActivity');

});