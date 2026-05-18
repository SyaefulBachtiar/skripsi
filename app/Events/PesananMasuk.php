<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PesananMasuk implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomChatId;

    /**
     * Create a new event instance.
     */
    public function __construct($roomChatId)
    {
        $this->roomChatId = $roomChatId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $room = \App\Models\ChatRooms::find($this->roomChatId);

        $recipientUserId = (auth()->id() === $room->technician->user_id) 
        ? $room->customer->user_id 
        : $room->technician->user_id;

        return [
            new PrivateChannel('servisio-chat.' . $this->roomChatId),
            new PrivateChannel('App.Models.User.' . $recipientUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PesananMasuk';
    }
}
