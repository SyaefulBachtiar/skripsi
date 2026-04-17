<?php

namespace App\Models\Role_users;

use App\Models\ChatRooms;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customer';

    protected $fillable = [
        'user_id',
        'saldo',
        'detail_alamat',
        'latitude',
        'longitude',
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order (): HasMany
    {
        return $this->hasMany(Order::class, 'id_customer', 'id');
    }

    public function chat_room (): HasMany
    {
        return $this->hasMany(ChatRooms::class, 'customer_id', 'id');
    }
}
