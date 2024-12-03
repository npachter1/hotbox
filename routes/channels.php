<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('App.User.{id}', function ($user, $id) {                    
    return $user->id === $id;
});

Broadcast::channel('message.{id}', function ($user, $id) {
    return $user->id === $user = User::findOrFail($id)->id;
});

Broadcast::channel('customersqueue', function ($user, $value) {
    return true;
});

Broadcast::channel('inventoryitem', function ($user, $value) {
    return true;
});

Broadcast::channel('customerupdated', function ($user, $value) {
    return true;
});
