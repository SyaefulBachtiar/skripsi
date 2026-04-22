<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LacakPesanan extends Model
{
    protected $table = 'lacak_pesanan';

    protected $fillable = [
        'id_order',
        'status_order',
        'note',
        'foto_bukti',
    ];

    public function order (): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id');
    }
}
