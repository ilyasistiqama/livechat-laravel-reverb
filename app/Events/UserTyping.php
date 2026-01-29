<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserTyping implements ShouldBroadcastNow
{
    public function __construct(
        public string $roomCode,
        public int $fromId,
        public string $fromType
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->roomCode}");
    }

    public function broadcastWith(): array
    {
        return [
            'from_id'   => $this->fromId,
            'from_type' => $this->fromType,
        ];
    }
}
