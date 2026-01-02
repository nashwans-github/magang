<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opd = \App\Models\Opd::where('slug', 'dinas-komunikasi-dan-informatika')->first();

        if ($opd) {
            \App\Models\Bidang::create([
                'opd_id' => $opd->id,
                'name' => 'Pengembangan Aplikasi (Software Development)',
                'description' => 'Bidang yang menangani pembuatan dan pemeliharaan aplikasi pemerintahan.',
            ]);

            \App\Models\Bidang::create([
                'opd_id' => $opd->id,
                'name' => 'Infrastruktur Jaringan',
                'description' => 'Bidang yang menangani jaringan internet dan infrastruktur server.',
            ]);
        }
    }
}
