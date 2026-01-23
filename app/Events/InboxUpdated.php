<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class InboxUpdated implements ShouldBroadcastNow
{
    public int $userId;
    public array $data;

    public function __construct(int $userId, array $data)
    {
        $this->userId = $userId;
        $this->data   = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('inbox.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'InboxUpdated';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
