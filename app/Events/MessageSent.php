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
        public array $chat
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->chat['room_code']);
    }

    public function broadcastWith()
    {
        return [
            'chat' => $this->chat
        ];
    }
}
