<?php 

/* dispensary/pos/drawers api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/drawers', 'DrawerController@index');
    Route::get('/drawers/batch', 'DrawerController@batch');
    Route::get('/drawers/export/{type}', 'DrawerController@exportGrid');
    Route::get('/drawers/{id}/export/{type}', 'DrawerController@exportView');
    
    Route::get('drawers/getOpenDrawer', 'DrawerController@getOpenDrawer');
    Route::post('drawers/{id}/{type}', 'DrawerController@action');

    Route::get('/drawers/{id}', 'DrawerController@show');
    Route::post('/drawers/batch/{type}', 'DrawerController@updateBatch');
    Route::post('/drawers', 'DrawerController@store');
    Route::put('/drawers/{id}', 'DrawerController@update');
    Route::delete('/drawers/{id}', 'DrawerController@destroy');

});