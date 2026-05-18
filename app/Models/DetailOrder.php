<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailOrder extends Model
{
    protected $table = 'detail_order';
    protected $fillable = [
        'id_order',
        'nama_layanan_tambahan',
        'harga_layanan_tambahan',
        'deskripsi',
        'foto',
        'acc_customer'
    ];

    public function order (): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
}
