<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1', 'as' => 'v1.'], function (){
    
    /* guest registration and login */
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
        
        Route::post('/login', 'AuthController@authenticate');
        Route::post('/logout', 'AuthController@logout');
        Route::post('/check', 'AuthController@check');
        Route::post('/register', 'AuthController@register');
        Route::get('/activate/{token}', 'AuthController@activate');
        Route::post('/password', 'AuthController@password');
        Route::post('/reactivateRequest','AuthController@reactivateRequest');
        Route::post('/validate-password-reset', 'AuthController@validatePasswordReset');
        Route::post('/reset', 'AuthController@reset');
        #Route::post('/social/token', 'SocialAuthController@getToken');
        Route::get('/clearLocation/{location}','AuthController@clearAuthLocation');
        
    });
    

    /* admin app location functions */
    Route::group(['middleware' => ['auth:api'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        
        
        /* auth [app-settings] routes */
        Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function (){
            
            Route::get('/locations/load','AuthController@loadAuthLocation');
            Route::get('/locations/change/{location}','AuthController@changeAuthLocation');
            Route::post('/location/printers/{id}', 'LocationController@updateLabelPrinter');
            Route::get('/location/ismedical/{id}', 'LocationController@isMedical');
            Route::get('/location/{id}', 'LocationController@show');
            Route::put('/location/{id}', 'LocationController@update');
            
            Route::get('/users', 'UserController@index');
            Route::get('/users/{id}/unarchive','UserController@unarchive');
            Route::get('/users/export/{type}', 'UserController@exportGrid');
            Route::get('/users/{id}/export/{type}', 'UserController@exportView');
            Route::get('/users/{id}', 'UserController@show');
            Route::put('/users/{id}', 'UserController@update');
            Route::post('/users/{id}/change-password', 'AuthController@changePassword');
            Route::post('/users/{id}/change-pin', 'AuthController@changePIN');
            Route::post('/users/{id}/permissions', 'UserController@hasPermissions');
            Route::post('/users/{id}/send-activation', 'AuthController@sendActivation');
            Route::post('/users/{id}', 'UserController@update');
            Route::post('/users', 'UserController@store');
            Route::delete('/users/{id}', 'UserController@destroy');

            Route::get('/task', 'TaskController@index');
            Route::get('/task/{id}', 'TaskController@show');
            Route::get('/task/export/{type}', 'TaskController@exportGrid');
            Route::post('/task', 'TaskController@store');
            Route::put('/task/{id}', 'TaskController@update');
            Route::delete('/task/{id}', 'TaskController@destroy');

            Route::get('/addressbook', 'AddressbookController@index');
            Route::get('/addressbook/batch', 'AddressbookController@batch');
            Route::get('/addressbook/export/{type}', 'AddressbookController@exportGrid');
            Route::get('/addressbook/{id}/export/{type}', 'UserController@exportView');
            Route::get('/addressbook/{id}', 'AddressbookController@show');
            Route::post('/addressbook/batch/{type}', 'AddressbookController@updateBatch');
            Route::post('/addressbook', 'AddressbookController@store');
            Route::put('/addressbook/{id}', 'AddressbookController@update');
            Route::delete('/addressbook/{id}', 'AddressbookController@destroy');
            
            Route::get('/toggleMode', 'AuthController@toggleMode');
            Route::post('/registerLocation', 'AuthController@registerLocation');
            Route::post('/newToken', 'AuthController@getANewToken');
            Route::get('/testMigrationConnection/{type}', 'AuthController@testMigrationConnection');
            Route::post('/startMigration/{type}', 'AuthController@startMigration');

            Route::group(['middleware' => ['permission:Store Admin Update']], function (){
                Route::get('/locations', 'LocationsController@index');
                Route::get('/locations/export/{type}', 'LocationsController@exportGrid');
                Route::get('/locations/{id}', 'LocationsController@show');
                Route::put('/locations/{id}', 'LocationsController@update');
                Route::post('modify/{id}/{type}','LocationsController@modify');
            });

            /* superadmin module section */
            Route::group(['middleware' => ['role:super-admin']], function (){
                        
                Route::get('/schemasform', 'SchemasController@list');
                Route::put('/schemasform/{code}', 'SchemasController@update');
                Route::delete('/schemasform/{code}', 'SchemasController@destroy');
            
            });

            Route::post('/pin', 'AuthController@validatePIN');

        });


        // HINT: all resource views routes in /resources folder, [module]_[model] namespaced




        /* common admin api endpoints */
        Route::get('/schemas/{module}/{list}','Auth\AuthController@loadSchemas');// load schemas (list scoped by module in backend!)
        Route::get('/regions/{country}', 'Auth\AuthController@getRegions');     // ajax get state/prov/regions for a country
        Route::post('/asset/{model}/{type}','Auth\AuthController@uploadAsset'); // image/file uploading
        Route::get('/graph/{name}', 'DashboardController@getGraph');            // graph data for index pages by name


    });
    

});
