<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Events\ChatReset;
use Carbon\Carbon;

class AutoResetChat extends Command
{
    protected $signature = 'chat:auto-reset';
    protected $description = 'Auto reset chat setelah melewati waktu tertentu';

    public function handle()
    {
        $limitTime = Carbon::now()->subHours(24); // contohnya 24 jam

        $chats = Chat::where('finished', false)
            ->where('created_at', '<', $limitTime)
            ->get();

        foreach ($chats as $chat) {
            $chat->finished = true;
            $chat->save();

            // Broadcast ke user terkait
            broadcast(new ChatReset($chat->from_id))->toOthers();
            broadcast(new ChatReset($chat->to_id))->toOthers();
        }

        $this->info('Auto reset chat selesai.');
    }
}
