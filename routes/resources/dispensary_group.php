<?php 

/* Dispensary/Loyalty/Groups api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Dispensary', 'prefix' => 'v1/admin/dispensary', 'as' => 'v1.admin.dispensary.'], function (){

    Route::get('/groups', 'GroupController@index');
    Route::get('/groups/filter', 'GroupController@getFilters');
    
    Route::get('/groups/export/{type}', 'GroupController@exportGrid');
    Route::get('/groups/{id}/export/{type}', 'GroupController@exportView');
    Route::get('/groups/{id}', 'GroupController@show');
 
    Route::post('/groups', 'GroupController@store');
    Route::post('/groups/filter', 'GroupController@filter');
    
    Route::put('/groups/{id}', 'GroupController@update');
    Route::delete('/groups/{id}', 'GroupController@destroy');

    Route::get('/customergroups', 'GroupController@customergroups');

});