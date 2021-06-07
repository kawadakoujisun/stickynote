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
    return (int) $user->id === (int) $id;
});

Broadcast::channel('todo-added-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('color-rect-update-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-create-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-destroy-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-info-item-pos-update-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-info-item-color-update-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-content-item-text-create-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-content-item-text-destroy-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-content-item-image-create-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('sticker-content-item-image-destroy-channel.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
