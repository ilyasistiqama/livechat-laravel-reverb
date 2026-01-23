<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ];
});

Broadcast::channel('inbox.{adminId}', function ($user, $adminId) {
    return (int) $user->id === (int) $adminId
        && $user->role === 'admin';
});
