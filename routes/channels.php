<?php

use App\Models\Chat;
use App\Services\AuthResolver;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{roomCode}', function ($user, string $roomCode) {
    $auth = AuthResolver::resolve();
    $type = $auth->type;

    return Chat::where('room_code', $roomCode)
        ->where(function ($q) use ($user, $type) {
            $q->where(function ($q) use ($user, $type) {
                $q->where('from_id', $user->id)
                    ->where('from_type', $type);
            })->orWhere(function ($q) use ($user, $type) {
                $q->where('to_id', $user->id)
                    ->where('to_type', $type);
            });
        })->exists();
}, ['guards' => ['admin', 'member']]);

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['admin', 'member']]);

Broadcast::channel('inbox.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['admin', 'member']]);
