<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Services\AuthResolver;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ChatInboxController extends Controller
{
    public function dashboard()
    {
        $auth = AuthResolver::resolve();

        $testimoni = Member::whereNot('id', $auth->user->id)->get();

        $payloadGlobal = Crypt::encrypt([
            'type' => 'customer-to-admin'
        ]);

        // ga perlu cek role apa-apa
        return view('dashboard', compact('testimoni', 'payloadGlobal'));
    }

    public function unread()
    {
        $auth = AuthResolver::resolve();
        if (!$auth) {
            return response()->json([]);
        }

        return response()->json(
            $this->buildInboxData($auth)
        );
    }

    public function buildInboxData($auth)
    {
        $userId = $auth->user->id;

        // ADMIN
        if ($auth->guard === 'admin') {
            return Chat::selectRaw('
        chats.room_code,
        chats.from_id,
        members.name as from_name,
        MAX(chats.message) as last_message,
        SUM(CASE WHEN chats.status != "read" THEN 1 ELSE 0 END) as unread
    ')
                ->join('members', 'members.id', '=', 'chats.from_id')
                ->where('chats.to_id', $userId)
                ->where('chats.finished', false)
                ->groupBy('chats.room_code', 'chats.from_id', 'members.name')
                ->orderByDesc('unread')
                ->get()
                ->map(function ($row) {
                    $payload = Crypt::encrypt([
                        'type' => 'customer-to-admin',
                        'to_member_id' => $row->from_id,
                        'page' => 'global',
                    ]);

                    $row->chat_href = route('chat.index', ['payload' => $payload]);
                    return $row;
                })
                ->toArray();
        }

        // MEMBER
        if ($auth->guard === 'member') {
            return Chat::selectRaw('
        chats.room_code,
        chats.from_id,
        users.name as from_name,
        MAX(chats.message) as last_message,
        SUM(CASE WHEN chats.status != "read" THEN 1 ELSE 0 END) as unread
    ')
                ->join('users', 'users.id', '=', 'chats.from_id')
                ->where('chats.to_id', $userId)
                ->where('chats.finished', false)
                ->where('chats.type', 'customer-to-customer')
                ->groupBy('chats.room_code', 'chats.from_id', 'users.name')
                ->orderByDesc('unread')
                ->get()
                ->map(function ($row) {
                    $payload = Crypt::encrypt([
                        'type' => 'customer-to-customer',
                        'to_member_id' => $row->from_id,
                        'page' => 'testimoni',
                    ]);

                    $row->chat_href = route('chat.index', ['payload' => $payload]);
                    return $row;
                })
                ->toArray();
        }

        return [];
    }
}
