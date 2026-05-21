<?php

namespace App\Models;

use App\Models\Role_users\Technician;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'review';

    protected $fillable = [
        'id_technician',
        'id_order',
        'id_jasa',
        'rating',
        'text_comment',
        'foto_review',
        'reply_comment',
    ];

    protected $casts = [
        'foto_review' => 'array',
    ];

    public function technician (): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'id_technician', 'id');
    }

    public function order (): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id');
    }

    public function jasa (): BelongsTo
    {
        return $this->belongsTo(Jasa::class, 'id_jasa', 'id');
    }
}
