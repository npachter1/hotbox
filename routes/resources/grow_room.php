<?php 

/* grow/warehouse/room api routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/rooms', 'RoomController@index');
    Route::get('/rooms/export/{type}', 'RoomController@exportGrid');
    
    Route::get('/rooms/{id}/export/{type}', 'RoomController@exportView');
    Route::get('/rooms/{id}', 'RoomController@show');
    Route::post('/rooms', 'RoomController@store');
    Route::put('/rooms/{id}', 'RoomController@update');
    Route::delete('/rooms/{id}', 'RoomController@destroy');

});