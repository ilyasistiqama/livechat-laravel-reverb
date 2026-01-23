<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatInboxController extends Controller
{
    public function admin()
    {
        return view('admin.chat-inbox');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function unread()
    {
        $data = Chat::selectRaw('
        chats.from_id,
        users.name as from_name,
        SUM(CASE WHEN chats.status != "read" THEN 1 ELSE 0 END) as unread
    ')
            ->join('users', 'users.id', '=', 'chats.from_id')
            ->where('chats.to_id', auth()->id())
            ->where('chats.finished', false)
            ->groupBy('chats.from_id', 'users.name')
            ->orderByDesc('unread')
            ->get();

        return response()->json($data);
    }
}
