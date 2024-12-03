<?php 

    Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Auth', 'prefix' => 'v1/admin/auth', 'as' => 'v1.admin.auth.'], function (){
        Route::get('/task', 'TaskController@index');
        Route::get('/task/{id}', 'TaskController@show')->name('show');
        Route::post('/task/store', 'TaskController@store')->name('store');
        Route::patch('/task/{id}', 'TaskController@update')->name('update');
        Route::delete('/task/{id}', 'TaskController@destroy')->name('destroy');
    });