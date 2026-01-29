<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Services\AuthResolver;
use Illuminate\Support\Facades\DB;

class ChatInboxController extends Controller
{
    public function admin()
    {
        return view('admin.chat-inbox');
    }

    public function dashboard()
    {
        // ga perlu cek role apa-apa
        return view('dashboard');
    }

    public function unread()
    {
        $auth = AuthResolver::resolve();

        if (!$auth) {
            return response()->json([]);
        }

        $userId = $auth->user->id;

        /**
         * ADMIN (guard: web)
         * chat dari MEMBER → join members
         */
        if ($auth->guard === 'admin') {
            $data = Chat::selectRaw('
                    chats.from_id,
                    members.name as from_name,
                    SUM(CASE WHEN chats.status != "read" THEN 1 ELSE 0 END) as unread
                ')
                ->join('members', 'members.id', '=', 'chats.from_id')
                ->where('chats.to_id', $userId)
                ->where('chats.finished', false)
                ->groupBy('chats.from_id', 'members.name')
                ->orderByDesc('unread')
                ->get();

            return response()->json($data);
        }

        /**
         * MEMBER (guard: member)
         * chat dari ADMIN → join users
         */
        if ($auth->guard === 'member') {
            $data = Chat::selectRaw('
                    chats.from_id,
                    users.name as from_name,
                    SUM(CASE WHEN chats.status != "read" THEN 1 ELSE 0 END) as unread
                ')
                ->join('users', 'users.id', '=', 'chats.from_id')
                ->where('chats.to_id', $userId)
                ->where('chats.finished', false)
                ->groupBy('chats.from_id', 'users.name')
                ->orderByDesc('unread')
                ->get();

            return response()->json($data);
        }

        return response()->json([]);
    }
}
