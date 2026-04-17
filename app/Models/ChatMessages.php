<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessages extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'message',
        'is_read'
    ];

    public function chat_room (): BelongsTo
    {
        return $this->belongsTo(ChatRooms::class, 'chat_room_id', 'id');
    }

    public function sender (): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }


}
