<?php

namespace App\Models\Role_users;

use App\Models\ChatRooms;
use App\Models\Jasa;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Technician extends Model
{

    protected $table = 'technician';

    protected $fillable = [
        'verifikasi',
        'user_id',
        'spesialisasi',
        'pengalaman',
        'sertifikat',
        'deskripsi',
        'detail_alamat',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'latitude',
        'longitude',
        'nama_asli',
        'foto_wajah',
        'foto_kegiatan',
        'alasa_ditolak'
    ];

    protected $casts = [
        'spesialisasi' => 'array',
        'sertifikat' => 'array',
        'pengalaman' => 'array',
        'foto_kegiatan' => 'array'
    ];

    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function jasa (): HasOne
    {
        return $this->hasOne(Jasa::class, 'id_technician', 'id');
    }

    public function chat_room (): HasMany
    {
        return $this->hasMany(ChatRooms::class, 'technician_id', 'id');
    }

    public function review (): HasMany
    {
        return $this->hasMany(Review::class, 'id_technician', 'id');
    }
}
