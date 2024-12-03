<?php 

/* grow/warehouse/strain routes */
Route::group(['middleware' => ['auth:api'],'namespace' => 'Api\V1\Grow', 'prefix' => 'v1/admin/grow', 'as' => 'v1.admin.grow.'], function (){

    Route::get('/strains', 'StrainController@index');
    Route::get('/strains/export/{type}', 'StrainController@exportGrid');
    
    Route::get('/strains/{id}/export/{type}', 'StrainController@exportView');
    Route::get('/strains/{id}', 'StrainController@show');
    Route::post('/strains', 'StrainController@store');
    Route::put('/strains/{id}', 'StrainController@update');
    Route::delete('/strains/{id}', 'StrainController@destroy');

});