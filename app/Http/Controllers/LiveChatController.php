<?php

namespace App\Http\Controllers;

use App\Events\ChatReset;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Member;
use App\Models\User;
use App\Services\AuthResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    public function index(Request $request)
    {
        $auth = AuthResolver::resolve();

        $type = $request->query('type', 'customer-to-admin');

        $roomCode   = null;
        $toUserId   = null;
        $toUserType = null;

        if ($type === 'customer-to-admin') {

            if ($auth->type === 'admin') {

                // ===============================
                // ADMIN LOGIN
                // ===============================
                $admin = $auth->user;

                $targetMemberId = $request->query('to_member_id');
                abort_if(!$targetMemberId, 404, 'Target member belum dipilih');

                // ADMIN boleh lihat room lama
                $existingChat = Chat::where(function ($q) use ($admin, $targetMemberId, $type) {
                    $q->where([
                        ['from_id', $targetMemberId],
                        ['from_type', 'member'],
                        ['to_id', $admin->id],
                        ['to_type', 'admin'],
                        ['type', $type],
                    ]);
                })->orWhere(function ($q) use ($admin, $targetMemberId, $type) {
                    $q->where([
                        ['from_id', $admin->id],
                        ['from_type', 'admin'],
                        ['to_id', $targetMemberId],
                        ['to_type', 'member'],
                        ['type', $type],
                    ]);
                })->latest()->first();

                $roomCode   = $existingChat?->room_code;
                $toUserId   = $targetMemberId;
                $toUserType = 'member';
            } else {

                $member = $auth->user;

                $existingChat = Chat::where(function ($q) use ($member, $type) {
                    $q->where([
                        ['from_id', $member->id],
                        ['from_type', 'member'],
                        ['type', $type],
                    ]);
                })->orWhere(function ($q) use ($member, $type) {
                    $q->where([
                        ['to_id', $member->id],
                        ['to_type', 'member'],
                        ['type', $type],
                    ]);
                })->latest()->first();

                $roomCode   = $existingChat?->room_code;
                $toUserId   = $existingChat
                    ? ($existingChat->from_type === 'admin'
                        ? $existingChat->from_id
                        : $existingChat->to_id)
                    : null;

                $toUserType = 'admin';
            }
        }


        return view('chat.index', compact(
            'roomCode',
            'toUserId',
            'toUserType',
            'type'
        ));
    }

    public function fetch(Request $request)
    {
        $roomCode = $request->room_code;
        if (!$roomCode) return response()->json(['chats' => []]);

        $chats = Chat::where('room_code', $roomCode)
            ->where('finished', false)
            ->orderBy('created_at')
            ->get();

        return response()->json(['chats' => $chats]);
    }

    public function send(Request $request)
    {
        $auth = AuthResolver::resolve();
        $type = $auth->type;

        if ($type === 'admin') {
            $request->validate([
                'to_id' => 'required|exists:members,id',
                'message' => 'required|string'
            ]);
            $toType = 'member';
        } else {
            $request->validate([
                'to_id' => 'required|exists:users,id',
                'message' => 'required|string'
            ]);
            $toType = 'admin';
        }

        // ===============================
        // CARI CHAT TERAKHIR (2 ARAH)
        // ===============================
        $lastChat = Chat::where(function ($q) use ($auth, $request, $type, $toType) {
            $q->where([
                ['from_id', $auth->user->id],
                ['from_type', $type],
                ['to_id', $request->to_id],
                ['to_type', $toType],
                ['type', $request->type],
            ]);
        })->orWhere(function ($q) use ($auth, $request, $type, $toType) {
            $q->where([
                ['from_id', $request->to_id],
                ['from_type', $toType],
                ['to_id', $auth->user->id],
                ['to_type', $type],
                ['type', $request->type],
            ]);
        })->latest()->first();

        // ===============================
        // TENTUKAN ROOM CODE
        // ===============================
        if (!$lastChat) {
            $roomCode = Str::uuid();
        } elseif (!$lastChat->finished) {
            $roomCode = $lastChat->room_code;
        } else {
            $roomCode = Str::uuid();
        }

        // ===============================
        // SIMPAN CHAT
        // ===============================
        $chat = Chat::create([
            'room_code' => $roomCode,
            'from_id'   => $auth->user->id,
            'from_type' => $type,
            'to_id'     => $request->to_id,
            'to_type'   => $toType,
            'message'   => $request->message,
            'type'      => $request->type,
            'finished'  => false,
        ]);

        broadcast(new MessageSent(
            chat: $chat->toArray(),
            roomCode: $roomCode
        ))->toOthers();

        return response()->json($chat);
    }


    public function markAsRead(Request $request)
    {
        $auth = AuthResolver::resolve();
        $roomCode = $request->room_code;
        if (!$roomCode) return response()->json(['status' => 'error']);

        Chat::where('room_code', $roomCode)
            ->where('to_id', $auth->user->id)
            ->update(['status' => 'read']);

        broadcast(new MessageRead($auth->user->id, $roomCode))->toOthers();

        return response()->json(['status' => 'ok']);
    }

    public function reset(Request $request)
    {
        $roomCode = $request->room_code;
        if (!$roomCode) return response()->json(['status' => 'error']);

        Chat::where('room_code', $roomCode)->update(['finished' => true]);

        broadcast(new ChatReset($roomCode))->toOthers();

        return response()->json(['status' => 'ok']);
    }
}
