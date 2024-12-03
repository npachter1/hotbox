<?php 

/* dispensary/products/category api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/categories', 'CategoryController@index');
    Route::get('/categories/batch', 'CategoryController@batch');
    Route::get('/categories/export/{type}', 'CategoryController@exportGrid');
    
    Route::get('/categories/syncMetrc', 'CategoryController@syncMetrcCategories');
    
    Route::get('/categories/{id}/export/{type}', 'CategoryController@exportView');
    Route::get('/categories/{id}', 'CategoryController@show');
    Route::post('/categories/batch/{type}', 'CategoryController@updateBatch');
    Route::post('/categories', 'CategoryController@store');
    Route::put('/categories/{id}', 'CategoryController@update');
    Route::delete('/categories/{id}', 'CategoryController@destroy');
    
    Route::post('/categories/exists', 'CategoryController@checkExist');
    
});