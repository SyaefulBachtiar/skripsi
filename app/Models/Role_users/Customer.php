<?php

namespace App\Models\Role_users;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
