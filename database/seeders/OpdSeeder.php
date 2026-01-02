<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Opd::create([
            'name' => 'Dinas Komunikasi dan Informatika',
            'slug' => 'dinas-komunikasi-dan-informatika',
            'address' => 'Jl. Jimerto No. 25-27, Surabaya',
            'phone' => '(031) 5312144',
            'operational_hours' => '08:00 - 16:00',
            'required_education' => 'Teknik Informatika, Sistem Informasi, Desain Komunikasi Visual',
            'document_requirements' => 'Surat Pengantar Kampus, Transkrip Nilai, CV',
            'description' => 'Dinas yang bertanggung jawab atas urusan komunikasi, informatika, statistik, dan persandian kota Surabaya.',
        ]);
    }
}
