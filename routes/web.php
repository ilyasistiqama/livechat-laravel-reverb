<?php

use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Events\ChatReset;
use App\Models\Chat;
use Illuminate\Http\Request;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CHAT ROUTES
    Route::get('/chat', [LiveChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch', [LiveChatController::class, 'fetch']);
    Route::post('/chat/send', [LiveChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/typing', function (\Illuminate\Http\Request $request) {
        broadcast(new \App\Events\UserTyping(auth()->id(), $request->to_id));
        return response()->noContent();
    })->name('chat.typing');
    Route::post('/chat/read', [LiveChatController::class, 'markAsRead'])->name('chat.read');

    Route::post('/chat/reset', function (Request $request) {
        $admin = $request->user();

        if ($admin->role !== 'admin') {
            abort(403);
        }

        $toId = $request->to_id;

        // update chat jadi finished
        Chat::where(function ($q) use ($admin, $toId) {
            $q->where('from_id', $admin->id)
                ->where('to_id', $toId);
        })->orWhere(function ($q) use ($admin, $toId) {
            $q->where('from_id', $toId)
                ->where('to_id', $admin->id);
        })->update(['finished' => true]);

        broadcast(new ChatReset($admin->id, $toId))->toOthers();

        return response()->json(['status' => 'ok']);
    });
});

require __DIR__ . '/auth.php';
