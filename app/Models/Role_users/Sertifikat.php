<?php

namespace App\Models\Role_users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'img'
    ];

    public function sertifikat_technician (): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }
}
