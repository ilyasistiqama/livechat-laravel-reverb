<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatReset implements ShouldBroadcastNow
{
    public string $roomCode;

    public function __construct(string $roomCode)
    {
        $this->roomCode = $roomCode;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->roomCode}");
    }

    public function broadcastWith(): array
    {
        return [
            'room_code' => $this->roomCode,
        ];
    }
}
