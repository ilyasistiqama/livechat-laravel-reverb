<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageRead implements ShouldBroadcastNow
{
    public int $chatId;
    public int $toId;

    public function __construct(int $chatId, int $toId)
    {
        $this->chatId = $chatId;
        $this->toId   = $toId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->toId);
    }
}
