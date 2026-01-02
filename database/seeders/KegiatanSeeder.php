<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesertaList = \App\Models\Peserta::all();
        $pembimbing = \App\Models\Pembimbing::first();

        foreach ($pesertaList as $peserta) {
            // Presensi 3 days
            for ($i = 0; $i < 3; $i++) {
                \App\Models\Presensi::create([
                    'peserta_id' => $peserta->id,
                    'date' => now()->subDays($i),
                    'check_in' => '08:00:00',
                    'check_out' => '16:00:00',
                    'status' => 'hadir',
                    'is_approved' => true,
                ]);
            }

            // Progress
            \App\Models\Progress::create([
                'peserta_id' => $peserta->id,
                'date' => now(),
                'title' => 'Membuat Fitur Login',
                'description' => 'Hari ini saya mengerjakan fitur login menggunakan Laravel Breeze.',
                'status' => 'approved',
            ]);

            // Penilaian (Example)
            if ($pembimbing) {
                \App\Models\Penilaian::create([
                    'peserta_id' => $peserta->id,
                    'pembimbing_id' => $pembimbing->id,
                    'attendance_score' => 90,
                    'discipline_score' => 85,
                    'task_completion_score' => 88,
                    'deadline_accuracy_score' => 90,
                    'independence_score' => 85,
                    'final_score' => 87.6,
                    'comments' => 'Mahasiswa sangat rajin dan kompeten.',
                ]);
            }

            // Surat Penerimaan
            \App\Models\Surat::create([
                'peserta_id' => $peserta->id,
                'type' => 'acceptance',
                'issued_date' => now()->subDays(10),
                'file_path' => 'surat/acceptance_default.pdf',
            ]);
        }
    }
}
