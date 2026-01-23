<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\ChatInboxController;

use App\Events\ChatReset;
use App\Models\Chat;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [ChatInboxController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

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
    | CHAT
    |-------------------------
    */
    Route::get('/chat', [LiveChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch', [LiveChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/send', [LiveChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/read', [LiveChatController::class, 'markAsRead'])->name('chat.read');

    Route::post('/chat/typing', function (Request $request) {
        broadcast(
            new \App\Events\UserTyping(auth()->id(), $request->to_id)
        );

        return response()->noContent();
    })->name('chat.typing');

    Route::post('/chat/reset', function (Request $request) {
        $admin = $request->user();

        abort_if($admin->role !== 'admin', 403);

        $toId = $request->to_id;

        Chat::where(function ($q) use ($admin, $toId) {
            $q->where('from_id', $admin->id)
                ->where('to_id', $toId);
        })->orWhere(function ($q) use ($admin, $toId) {
            $q->where('from_id', $toId)
                ->where('to_id', $admin->id);
        })->update(['finished' => true]);

        broadcast(new ChatReset($admin->id, $toId))->toOthers();

        return response()->json(['status' => 'ok']);
    })->name('chat.reset');

    /*
    |-------------------------
    | CHAT INBOX
    |-------------------------
    */
    Route::get('/chat/unread', [ChatInboxController::class, 'unread'])
        ->name('chat.unread');

    Route::get('/admin/inbox', [ChatInboxController::class, 'admin'])
        ->middleware('can:isAdmin')
        ->name('admin.inbox');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
