<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Membuat Akun Admin Utama
        User::create([
            'name'      => 'Syaeful Admin',
            'email'     => 'admin@servisio.com',
            'password'  => Hash::make('password123'), // Ganti dengan password yang lebih kuat
            'role'      => 'admin', // Sesuaikan dengan nama kolom role di tabel users kamu
        ]);

        // Opsional: Membuat Akun Teknisi untuk Testing
        User::create([
            'name'      => 'Teknisi Demo',
            'email'     => 'teknisi@servisio.com',
            'password'  => Hash::make('password123'),
            'role'      => 'technician',
        ]);

        // Opsional: Membuat Akun Pelanggan untuk Testing
        User::create([
            'name'      => 'Pelanggan Demo',
            'email'     => 'customer@gmail.com',
            'password'  => Hash::make('password123'),
            'role'      => 'customer',
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
