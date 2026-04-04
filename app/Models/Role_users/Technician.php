<?php

namespace App\Models\Role_users;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Technician extends Model
{

    protected $table = 'technician';

    protected $fillable = [
        'user_id',
        'spesialisasi',
        'pengalaman',
        'sertifikat',
        'deskripsi',
        'detail_alamat',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'spesialisasi' => 'array',
        'sertifikat' => 'array',
        'pengalaman' => 'array'
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
