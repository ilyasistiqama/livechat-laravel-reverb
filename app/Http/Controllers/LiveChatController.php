<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'customer') {
            $toUser = User::where('role', 'admin')->firstOrFail();
        } else {
            // ADMIN WAJIB pilih customer
            $customerId = $request->query('customer_id');

            if (!$customerId) {
                abort(404, 'Customer belum dipilih');
            }

            $toUser = User::where('id', $customerId)
                ->where('role', 'customer')
                ->firstOrFail();
        }

        $chats = Chat::where(function ($q) use ($user, $toUser) {
            $q->where('from_id', $user->id)
                ->where('to_id', $toUser->id);
        })->orWhere(function ($q) use ($user, $toUser) {
            $q->where('from_id', $toUser->id)
                ->where('to_id', $user->id);
        })
            ->where('finished', false)
            ->orderBy('created_at')
            ->get();

        return view('chat.index', compact('toUser'));
    }

    public function fetch(Request $request)
    {
        $user = auth()->user();
        $toId = $request->customer_id;

        $chats = Chat::where(function ($q) use ($user, $toId) {
            $q->where(function ($qq) use ($user, $toId) {
                $qq->where('from_id', $user->id)
                    ->where('to_id', $toId);
            })
                ->orWhere(function ($qq) use ($user, $toId) {
                    $qq->where('from_id', $toId)
                        ->where('to_id', $user->id);
                });
        })
            ->where('finished', false)
            ->orderBy('created_at')
            ->get();

        return response()->json(['chats' => $chats]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'to_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $chat = Chat::create([
            'from_id' => auth()->id(),
            'to_id' => $request->to_id,
            'message' => $request->message,
            'status' => 'sent',
            'finished' => false
        ]);

        broadcast(new MessageSent($chat))->toOthers();

        return response()->json($chat);
    }

    public function markAsRead(Request $request)
    {
        $request->validate(['from_id' => 'required|exists:users,id']);

        Chat::where('from_id', $request->from_id)
            ->where('to_id', auth()->id())
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);

        broadcast(new MessageRead($request->from_id, auth()->id()))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
