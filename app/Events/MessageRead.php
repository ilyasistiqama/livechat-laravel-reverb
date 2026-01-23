<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageRead implements ShouldBroadcastNow
{
    public int $readerId;
    public int $senderId;

    public function __construct(int $readerId, int $senderId)
    {
        $this->readerId = $readerId;
        $this->senderId = $senderId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->senderId);
    }

    public function broadcastAs(): string
    {
        return 'MessageRead';
    }
}
