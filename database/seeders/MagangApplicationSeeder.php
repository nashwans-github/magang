<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MagangApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opd = \App\Models\Opd::first();
        
        // Pemohon (Pending)
        $pemohon = \App\Models\User::where('email', 'siti@gmail.com')->first();
        if ($pemohon && $opd) {
            \App\Models\MagangApplication::create([
                'user_id' => $pemohon->id,
                'opd_id' => $opd->id,
                'institution_name' => 'Universitas Airlangga',
                'start_date' => now()->addMonth(),
                'end_date' => now()->addMonths(4),
                'status' => 'pending',
                'documents' => [],
            ]);
        }

        // Peserta 1 (Approved -> Active)
        $peserta1 = \App\Models\User::where('email', 'ahmad@gmail.com')->first();
        if ($peserta1 && $opd) {
            \App\Models\MagangApplication::create([
                'user_id' => $peserta1->id,
                'opd_id' => $opd->id,
                'institution_name' => 'Institut Teknologi Sepuluh Nopember',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => 'approved',
                'documents' => [],
            ]);
        }

        // Peserta 2 (Approved -> Active)
        $peserta2 = \App\Models\User::where('email', 'dewi@gmail.com')->first();
        if ($peserta2 && $opd) {
            \App\Models\MagangApplication::create([
                'user_id' => $peserta2->id,
                'opd_id' => $opd->id,
                'institution_name' => 'UPN Veteran Jawa Timur',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => 'approved',
                'documents' => [],
            ]);
        }
    }
}
