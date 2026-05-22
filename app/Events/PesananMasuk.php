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
    public $recipientUserId;

    /**
     * Create a new event instance.
     */
    public function __construct($roomChatId, $recipientUserId = null)
    {
        $this->roomChatId = $roomChatId;
        $this->recipientUserId = $recipientUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('servisio-chat.' . $this->roomChatId),
        ];

        // Hanya tambah user channel kalau recipientUserId dikirim
        if ($this->recipientUserId) {
            $channels[] = new PrivateChannel('App.Models.User.' . $this->recipientUserId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'PesananMasuk';
    }
}
