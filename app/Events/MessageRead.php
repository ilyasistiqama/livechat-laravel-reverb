<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageRead implements ShouldBroadcastNow
{
    public string $roomCode;
    public int $readerId;

    public function __construct(int $readerId, string $roomCode)
    {
        $this->readerId = $readerId;
        $this->roomCode = $roomCode;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->roomCode}");
    }

    public function broadcastWith(): array
    {
        return [
            'reader_id' => $this->readerId,
        ];
    }
}
