<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opd = \App\Models\Opd::first();

        if ($opd) {
            \App\Models\Berita::create([
                'opd_id' => $opd->id,
                'title' => 'Penerimaan Magang Batch 1 Tahun 2026',
                'slug' => 'penerimaan-magang-batch-1-2026',
                'content' => 'Diskominfo Surabaya membuka kesempatan magang bagi mahasiswa tingkat akhir...',
                'is_published' => true,
            ]);

            \App\Models\Berita::create([
                'opd_id' => $opd->id,
                'title' => 'Mahasiswa Magang Membuat Aplikasi Smart City',
                'slug' => 'mahasiswa-magang-membuat-aplikasi-smart-city',
                'content' => 'Mahasiswa magang di Diskominfo berhasil mengembangkan modul baru untuk...',
                'is_published' => true,
            ]);
        }
    }
}
