<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatReset implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public int $userA;
    public int $userB;

    public function __construct(int $userA, int $userB)
    {
        $this->userA = $userA;
        $this->userB = $userB;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->userA),
            new PrivateChannel('chat.' . $this->userB),
        ];
    }
}
