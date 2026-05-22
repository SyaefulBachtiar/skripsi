<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Role_users\Admin;
use App\Models\Role_users\Customer;
use App\Models\Role_users\Technician;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [

        'password',
        'remember_token',
        'google_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }


    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            // Cek data role dan avatar
            $avatar = $this->avatar;
            $role = $this->role;

            // 1. Jika avatar KOSONG
            if (!$avatar) {
                return $role === 'technician' 
                    ? asset('assets/default_profile/default_profile_teknisi.webp') 
                    : null;
            }

            // 2. Jika avatar adalah URL (Google)
            if (Str::startsWith($avatar, ['http://', 'https://'])) {
                if ($role === 'technician') {
                    // PAKSA teknisi pakai gambar default walaupun login Google
                    return asset('assets/default_profile/default_profile_teknisi.webp');
                }
                return $avatar;
            }

            // 3. Jika file lokal (Hasil upload manual)
            return asset('storage/' . $avatar);
        });
    }

    // Role Technician
    public function technician (): HasOne
    {
        return $this->hasOne(Technician::class, 'user_id', 'id');
    }

    // Role Customer
    public function customer (): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    // Role Admin
    public function admin (): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function chat_message (): HasMany
    {
        return $this->hasMany(ChatMessages::class, 'sender_id', 'id');
    }
}
