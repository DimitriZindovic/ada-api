<?php

namespace App\Events;

use App\Models\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('group.' . $this->message->group_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'sender' => [
                'firstName' => $this->message->sender->first_name,
                'lastName' => $this->message->sender->last_name,
                'email' => $this->message->sender->email,
                'phone' => $this->message->sender->phone,
                'createdAt' => $this->message->sender->created_at
            ],
            'group' => [
                'id' => $this->message->group->id,
                'name' => $this->message->group->name,
                'createdAt' => $this->message->group->created_at
            ],
            'createdAt' => $this->message->created_at,
        ];
    }
}
