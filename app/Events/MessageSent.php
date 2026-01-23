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

    public function broadcastWith()
    {
        return [
            'chat' => [
                'id' => $this->chat->id,
                'from_id' => $this->chat->from_id,
                'to_id' => $this->chat->to_id,
                'message' => $this->chat->message,
                'created_at' => $this->chat->created_at->toISOString(),
                'from_user' => [
                    'id' => $this->chat->fromUser->id,
                    'name' => $this->chat->fromUser->name,
                ],
            ]
        ];
    }
}
