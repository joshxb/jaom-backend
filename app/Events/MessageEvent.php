<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation_id;
    public $body;
    public $sender_id;
    public $created_at;
    public $group_id;
    public $other_user_name_in_group;

    public function __construct(
        $conversation_id,
        $message,
        $other_user_id,
        $group_id,
        $other_user_name_in_group
    ) {
        $this->conversation_id = $conversation_id;
        $this->body = $message;
        $this->sender_id = $other_user_id;
        $this->created_at = Carbon::now();
        $this->group_id = $group_id;
        $this->other_user_name_in_group = $other_user_name_in_group;
    }

    public function broadcastOn()
    {
        return ['chat'];
    }

    public function broadcastAs()
    {
        return 'message';
    }
}
