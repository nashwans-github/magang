<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Pusat
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@surabaya.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin_pusat',
        ]);

        // Admin OPD
        User::create([
            'name' => 'Admin Diskominfo',
            'email' => 'diskominfo@surabaya.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin_opd',
        ]);

        // Pembimbing
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@surabaya.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin_pembimbing',
        ]);

        // Pemohon (Belum diterima)
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'pemohon',
        ]);

        // Peserta (Sudah diterima)
        User::create([
            'name' => 'Ahmad Rizki',
            'email' => 'ahmad@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'peserta',
        ]);
        
        // Peserta 2 (Sudah diterima)
        User::create([
            'name' => 'Dewi Putri',
            'email' => 'dewi@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'peserta',
        ]);
    }
}
