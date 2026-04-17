<?php

namespace App\Models;

use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRooms extends Model
{
    protected $table = 'chat_rooms';

    protected $fillable = [
        'order_id',
        'customer_id',
        'technician_id',
        'status'
    ];

    public function order (): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function customer (): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function technician (): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id', 'id');
    }

    public function chat_message (): HasMany
    {
        return $this->hasMany(ChatRooms::class, 'chat_room_id', 'id'); 
    }

}
