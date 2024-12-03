<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* 3rd party login pages */
Route::get('/auth/social/{provider}', 'SocialAuthController@providerRedirect');
Route::get('/auth/{provider}/callback', 'SocialAuthController@providerRedirectCallback');


/* guest processing (and other output/stand-alone) pages */
Route::get('/hotboxiconlibrary', 'Controller@iconHelp')->name('hotboxiconlibrary'); // for development purposes - remove for production!

// Notifications
Route::post('notifications', 'Notification\NotificationController@store');
Route::get('notifications', 'Notification\NotificationController@index');
Route::patch('notifications/{id}/read', 'Notification\NotificationController@markAsRead');
Route::post('notifications/mark-all-read', 'Notification\NotificationController@markAllRead');
Route::post('notifications/{id}/dismiss', 'Notification\NotificationController@dismiss');
// Push Subscriptions
Route::post('subscriptions', 'Notification\PushSubscriptionController@update');
Route::post('subscriptions/delete', 'Notification\PushSubscriptionController@destroy');



/* [Vue] App Admin Portal */
Route::get('/{vue?}', function () {
    return view('admin');
})->where('vue', '[\/\w\.-]*')->name('admin');

Route::get('login',['as' => 'login', function () {
    return view('admin');
}]);
