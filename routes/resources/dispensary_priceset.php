<?php 

/* dispensary/products/priceset api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/pricesets', 'PricesetController@index');
    Route::get('/pricesets/export/{type}', 'PricesetController@exportGrid');
    Route::get('/pricesets/{id}/export/{type}', 'PricesetController@exportView');
    Route::get('/pricesets/{id}', 'PricesetController@show');
    Route::post('/pricesets', 'PricesetController@store');
    Route::put('/pricesets/{id}', 'PricesetController@update');
    Route::delete('/pricesets/{id}', 'PricesetController@destroy');

});