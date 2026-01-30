<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class InboxUpdated implements ShouldBroadcastNow
{
    public int $userId;
    public array $payload;

    public function __construct(int $userId, array $payload)
    {
        $this->userId  = $userId;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('inbox.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'InboxUpdated';
    }

    public function broadcastWith()
    {
        return $this->payload;
    }
}
