<?php 

/* dispensary/loyalty/reward api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/rewards', 'RewardController@index');
    Route::get('/rewards/batch', 'RewardController@batch');
    Route::get('/rewards/export/{type}', 'RewardController@exportGrid');
    Route::get('/rewards/{id}/export/{type}', 'RewardController@exportView');
    Route::get('/rewards/{id}', 'RewardController@show');
    Route::post('/rewards/batch/{type}', 'RewardController@updateBatch');
    Route::post('/rewards', 'RewardController@store');
    Route::put('/rewards/{id}', 'RewardController@update');
    Route::delete('/rewards/{id}', 'RewardController@destroy');
    
    Route::get('/rewardstriggers', 'RewardController@triggerShow');
    Route::post('/rewardstriggers', 'RewardController@triggerUpdate');

});