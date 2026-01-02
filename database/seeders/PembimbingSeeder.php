<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PembimbingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('email', 'budi@surabaya.go.id')->first();
        $bidang = \App\Models\Bidang::where('name', 'like', '%Pengembangan Aplikasi%')->first();

        if ($user && $bidang) {
            \App\Models\Pembimbing::create([
                'user_id' => $user->id,
                'bidang_id' => $bidang->id,
                'nip' => '19850101 201001 1 001',
                'position' => 'Kepala Seksi Pengembangan',
                'phone' => '081234567890',
            ]);
        }
    }
}
