<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load('user', 'conversation');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'user_id' => $this->message->user_id,
                'user_name' => $this->message->user->name,
                'message' => $this->message->message,
                'type' => $this->message->type,
                'file_name' => $this->message->file_name,
                'file_size' => $this->message->getFormattedFileSize(),
                'file_icon' => $this->message->getFileIcon(),
                'is_file' => $this->message->isFile(),
                'created_at' => $this->message->created_at->format('H:i'),
                'created_at_human' => $this->message->created_at->diffForHumans(),
            ]
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
