<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserTyping implements ShouldBroadcastNow
{
    public int $fromId;
    public int $toId;

    public function __construct(int $fromId, int $toId)
    {
        $this->fromId = $fromId;
        $this->toId   = $toId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->toId),
        ];
    }
}
