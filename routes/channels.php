<?php

use App\Models\ChatPair;
use App\Services\AuthResolver;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{roomCode}', function ($user, string $roomCode) {
    $auth = AuthResolver::resolve();
    if (!$auth) return false;

    // cek apakah user ada di room
    return \App\Models\Chat::where('room_code', $roomCode)
        ->where(function ($q) use ($auth) {
            $q->where('from_id', $auth->user->id)
                ->where('from_type', $auth->type)
                ->orWhere('to_id', $auth->user->id)
                ->where('to_type', $auth->type);
        })->exists();
}, ['guards' => ['admin', 'member']]);

// Broadcast::channel('online', function () {

//     $auth = AuthResolver::resolve();

//     if (! $auth) {
//         return false;
//     }

//     return [
//         'id'   => $auth->type . '-' . $auth->id, // WAJIB unik
//         'name' => $auth->name,
//         'role' => $auth->role,
//         'type' => $auth->type, // admin / member
//     ];
// }, ['guards' => ['admin', 'member']]);


Broadcast::channel('inbox.{adminId}', function ($user, $adminId) {

    if (! $user) {
        return false;
    }

    return $user instanceof \App\Models\User
        && (int) $user->id === (int) $adminId;
}, ['guards' => ['admin']]);
