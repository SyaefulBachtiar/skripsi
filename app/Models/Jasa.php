<?php

namespace App\Models;

use App\Models\Role_users\Technician;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Jasa extends Model
{
    protected $table = 'jasa';

    protected $fillable = [
        'id_technician',
        'nama_jasa',
        'harga_jasa',
        'deskripsi',
        'thumbnails',
        'ketersediaan_tanggal',
        'ketersediaan_jam',
        'is_setiap_hari',
        'layanan_tambahan',
        'keluhan'
    ];

    protected $casts = [
        'thumbnails' => 'array',
        'ketersediaan_tanggal' => 'array',
        'ketersediaan_jam' => 'array',
        'layanan_tambahan' => 'array',
        'keluhan' => 'array',
        'is_setiap_hari' => 'boolean',
    ];

    protected function firstThumbnail (): Attribute
    {
        return Attribute::get(function () {
            return $this->thumbnails[0] ?? null;
        });
    }

    protected function ketersediaanStatus (): Attribute
    {
        return Attribute::get(function () {
            // Jika diset setiap hari, maka selalu tersedia
            if ($this->is_setiap_hari) {
                return 'Tersedia';
            }

            // Jika array tanggal kosong
            if (empty($this->ketersediaan_tanggal)) {
                if(!$this->is_setiap_hari) {
                    return 'Tersedian';
                } else {
                    return 'Jadwal belum diatur';
                }
            }

            // Ambil tanggal terakhir dari array (tanggal paling jauh)
            $tanggalTerakhir = max($this->ketersediaan_tanggal);

            // Bandingkan dengan tanggal hari ini (abaikan jam)
            if (Carbon::parse($tanggalTerakhir)->isPast() && !Carbon::parse($tanggalTerakhir)->isToday()) {
                return 'Ketersediaan perlu diperbarui';
            }

            return 'Tersedia';
        });
    }

    public function technician (): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'id_technician', 'id');
    }

    public function order (): HasMany
    {
        return $this->hasMany(Order::class, 'id_jasa', 'id');
    }

    public function review (): HasMany
    {
        return $this->hasMany(Review::class, 'id_jasa', 'id');
    }
}
