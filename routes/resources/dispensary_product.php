<?php 

/* dispensary/products/product api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/products', 'ProductController@index');
    Route::get('/products/batch', 'ProductController@batch');
    Route::get('/products/export/{type}', 'ProductController@exportGrid');
    Route::get('/products/{id}/export/{type}', 'ProductController@exportView');
    Route::get('/products/{id}', 'ProductController@show');
    Route::post('/products/batch/{type}', 'ProductController@updateBatch');
    Route::post('/products', 'ProductController@store');
    Route::put('/products/{id}', 'ProductController@update');
    Route::delete('/products/{id}', 'ProductController@destroy');
    
    Route::post('/products/exists', 'ProductController@checkExist');

});