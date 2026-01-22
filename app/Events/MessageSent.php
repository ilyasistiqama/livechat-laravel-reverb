<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    public Chat $chat;

    public function __construct(Chat $chat)
    {
        Log::info('EVENT CONSTRUCT', ['chat_id' => $chat->id]);
        $this->chat = $chat;
    }

    public function broadcastOn(): array
    {
        Log::info('BROADCAST ON', [
            'to' => $this->chat->to_id,
            'from' => $this->chat->from_id,
        ]);

        return [
            new PrivateChannel('chat.' . $this->chat->to_id),
        ];
    }
}
