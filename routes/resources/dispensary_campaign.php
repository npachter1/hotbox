<?php 

/* dispensary/loyalty/campaigns api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/campaigns', 'CampaignController@index');
    Route::get('/campaigns/batch', 'CampaignController@batch');
    Route::get('/campaigns/export/{type}', 'CampaignController@exportGrid');
    Route::get('/campaigns/{id}/export/{type}', 'CampaignController@exportView');
    Route::get('/campaigns/{id}', 'CampaignController@show');
    Route::post('/campaigns/test', 'CampaignController@testSMS');
    Route::post('/campaigns/batch/{type}', 'CampaignController@updateBatch');
    Route::post('/campaigns', 'CampaignController@store');
    Route::post('/campaigns/generate', 'CampaignController@autoGenerate');
    Route::put('/campaigns/{id}', 'CampaignController@update');
    Route::delete('/campaigns/{id}', 'CampaignController@destroy');

    Route::get('/campaigns/requeue/{id}','CampaignController@requeue');

});