<?php

use Illuminate\Support\Facades\Broadcast;

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


Broadcast::channel('my-channel.{id}', function ($id) {
    return true;
});
Broadcast::channel('my-channel1', function ($id = null) {
    return true;
});
// Broadcast::channel('video-channel', function ($id) {
//     return true;
// });
// Broadcast::channel('my-channel', function () {
//     return true;
// });

// Broadcast::channel('test', function () {
//     return Auth::check();
// });
