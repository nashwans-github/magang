<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = \App\Models\MagangApplication::where('status', 'approved')->get();
        $bidang = \App\Models\Bidang::first(); // Assign to first bidang for example

        foreach ($applications as $app) {
            if ($bidang) {
                \App\Models\Peserta::create([
                    'user_id' => $app->user_id,
                    'magang_application_id' => $app->id,
                    'bidang_id' => $bidang->id,
                    'major' => 'Teknik Informatika',
                    'student_id_number' => '123456789' . $app->id,
                    'status' => 'active',
                ]);
            }
        }
    }
}
