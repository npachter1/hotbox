<?php 

/* dispensary/loyalty/discount api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/discounts', 'DiscountController@index');
    Route::get('/discounts/batch', 'DiscountController@batch');
    Route::get('/discounts/export/{type}', 'DiscountController@exportGrid');
    Route::get('/discounts/{id}/export/{type}', 'DiscountController@exportView');
    Route::get('/discounts/{id}', 'DiscountController@show');
    Route::post('/discounts/batch/{type}', 'DiscountController@updateBatch');
    Route::post('/discounts', 'DiscountController@store');
    Route::put('/discounts/{id}', 'DiscountController@update');
    Route::delete('/discounts/{id}', 'DiscountController@destroy');
    
    Route::post('/discounts/exists', 'DiscountController@checkExist');

});