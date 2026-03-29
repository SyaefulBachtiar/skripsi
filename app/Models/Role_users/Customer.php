<?php

namespace App\Models\Role_users;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'pref_lat',
        'pref_lng',
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
