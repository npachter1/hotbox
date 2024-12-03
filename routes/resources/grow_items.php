<?php 

/* grow/warehouse/items routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/items', 'ItemController@index');
    Route::get('/items/export/{type}', 'ItemController@exportGrid');
    
    Route::get('/items/{id}/export/{type}', 'ItemController@exportView');
    Route::get('/items/{id}', 'ItemController@show');
    Route::post('/items', 'ItemController@store');
    Route::put('/items/{id}', 'ItemController@update');
    Route::delete('/items/{id}', 'ItemController@destroy');

});