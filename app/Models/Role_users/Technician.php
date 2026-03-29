<?php

namespace App\Models\Role_users;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Technician extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'experience_year',
        'location_lat',
        'location_lng',
        'rating_avg',
        'total_reviews'
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
