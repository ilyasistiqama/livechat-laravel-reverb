<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Member;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    public function __construct(
        public array $chat,
        public string $roomCode
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->roomCode}");
    }

    public function broadcastWith(): array
    {
        return [
            'chat' => $this->chat
        ];
    }
}
