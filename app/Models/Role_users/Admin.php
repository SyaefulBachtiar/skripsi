<?php

namespace App\Models\Role_users;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $table = 'admin';

    protected $fillable = [
        'user_id',
        'last_login'
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
