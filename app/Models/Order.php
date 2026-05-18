<?php

namespace App\Models;

use App\Models\Role_users\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'order';

    protected $fillable = [
        'id_customer',
        'id_jasa',
        'order_date',
        'order_time',
        'keluhan',
        'layanan_tambahan',
        'total_harga',
    ];

    protected $casts = [
        'order_date' => 'date',
        'keluhan' => 'array',
        'layanan_tambahan' => 'array',
        'total_harga' => 'decimal:2'
    ];

    public function customer (): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }

    public function jasa (): BelongsTo
    {
        return $this->belongsTo(Jasa::class, 'id_jasa', 'id');
    }

    public function chat_room (): HasOne
    {
        return $this->hasOne(ChatRooms::class, 'order_id', 'id');
    }

    public function lacak_pesanan (): HasMany
    {
        return $this->hasMany(LacakPesanan::class, 'id_order', 'id');
    }

    public function review (): HasMany
    {
        return $this->hasMany(Review::class, 'id_order', 'id');
    }

    public function latestStatus()
    {
        // Mengambil satu data terbaru dari tabel lacak_pesanan
        return $this->hasOne(LacakPesanan::class, 'id_order')->latestOfMany();
    }

    public function detail_order (): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'id_order');
    }
}
