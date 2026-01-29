<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\ChatInboxController;
use App\Services\AuthResolver;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [ChatInboxController::class, 'dashboard'])
    ->middleware('auth.any')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware('auth.any')->group(function () {

    /*
    |-------------------------
    | PROFILE
    |-------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |-------------------------
    | CHAT (PAIR BASED)
    |-------------------------
    */

    // 👉 buka chat room
    Route::get('/chat', [LiveChatController::class, 'index'])
        ->name('chat.index');

    // 👉 fetch message
    Route::get('/chat/fetch', [LiveChatController::class, 'fetch'])
        ->name('chat.fetch');

    // 👉 send message
    Route::post('/chat/send', [LiveChatController::class, 'send'])
        ->name('chat.send');

    // 👉 reset chat (ADMIN ONLY)
    Route::post('/chat/reset', [LiveChatController::class, 'reset'])
        // ->middleware('can:isAdmin')
        ->name('chat.reset');

    Route::post('/chat/read', [LiveChatController::class, 'markAsRead'])
        ->name('chat.read');

    /*
    |-------------------------
    | TYPING (PAIR BASED)
    |-------------------------
    */
    Route::post('/chat/typing', function (Request $request) {
        $auth = \App\Services\AuthResolver::resolve();

        broadcast(new \App\Events\UserTyping(
            roomCode: $request->room_code,
            fromId: $auth->user->id,
            fromType: $auth->type
        ))->toOthers();

        return response()->noContent();
    });



    /*
    |-------------------------
    | INBOX
    |-------------------------
    */
    Route::get('/chat/unread', [ChatInboxController::class, 'unread'])
        ->name('chat.unread');

    Route::get('/admin/inbox', [ChatInboxController::class, 'admin'])
        ->middleware('can:isAdmin')
        ->name('admin.inbox');
});
