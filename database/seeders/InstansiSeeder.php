<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opd;
use App\Models\Bidang;
use Illuminate\Support\Str;

class InstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instansis = [
            [
                'name' => "Dinas Komunikasi dan Informatika",
                'slug' => 'komunikasi-informatika',
                'description' => "DINKOMINFO Surabaya bertugas mengelola jaringan internet pemerintah, mengembangkan aplikasi layanan publik, mengelola situs web dan sosial media resmi Pemkot Surabaya, serta mengoperasikan layanan darurat Command Center 112.",
                'address' => "Jl. Jimerto No. 25-27 Lantai 5. Surabaya, Jawa Timur 60272",
                'phone' => "(031) 5312144",
                'operational_hours' => "08.00 - 16.00",
                'required_education' => "SMK, Mahasiswa S1/D4 Sederajat",
                'document_requirements' => ["KTP", "Proposal", "Surat Pengantar", "CV"],
                'documentation_images' => [
                     '/storage/kominfo1.png',
                     '/storage/kominfo2.png',
                     '/storage/kominfo3.png',
                ],
                // Update existing record if found by slug, or create new
                'fields' => [
                    ['name' => 'Sosial Media', 'icon' => '📱'],
                    ['name' => 'Desain', 'icon' => '🎨'],
                    ['name' => 'Administrasi', 'icon' => '📝'],
                    ['name' => 'Live Streaming', 'icon' => '📹'],
                    ['name' => 'Data Sains', 'icon' => '📊'],
                    ['name' => 'Website', 'icon' => '🌐'],
                    ['name' => 'Aplikasi', 'icon' => '📱'],
                    ['name' => 'Jaringan', 'icon' => '🔌'],
                    ['name' => 'Broadband Learning Center', 'icon' => '🏫'],
                ]
            ],
            [
                'name' => "Dinas Pendidikan",
                'slug' => 'pendidikan',
                'description' => "DISPENDIK Surabaya bertanggung jawab penuh untuk mengelola sekolah negeri, membina tenaga pendidik (Guru), menyusun kurikulum muatan lokal, mendistribusikan bantuan pendidikan, serta mengawasi kualitas pembelajaran di seluruh kota.",
                'address' => "Jl. Jagir Wonokromo No. 354-356, Surabaya",
                'phone' => "(031) 8418904",
                'operational_hours' => "07.30 - 16.00",
                'required_education' => "Mahasiswa S1 (Pendidikan)",
                'document_requirements' => ["Surat Pengantar", "Tansfer Nilai", "CV"],
                'documentation_images' => [
                     '/storage/dinpen1.png',
                     '/storage/dinpen2.png',
                     '/storage/dinpen3.png',
                ],
                'fields' => [
                     ['name' => 'Administrasi Sekolah', 'icon' => '🏫'],
                     ['name' => 'Kurikulum', 'icon' => '📚']
                ]
            ],
            [
                'name' => "Dinas Kesehatan",
                'slug' => 'kesehatan',
                'description' => "DINKES Surabaya bertugas untuk mengelola Pusat Kesehatan Masyarakat (Puskesmas) yang ada di seluruh kecamatan, menjalankan program vaksinasi, mengendalikan penyakit menular, mengawasi kesehatan lingkungan.",
                'address' => "Jl. Jemursari No. 197, Surabaya",
                'phone' => "(031) 8439473",
                'operational_hours' => "08.00 - 16.00",
                'required_education' => "D3/S1 Kesehatan Masyarakat/Kedokteran",
                'document_requirements' => ["KTP", "Surat Pengantar", "Transkrip"],
                'documentation_images' => [
                     '/storage/dinkes1.png',
                     '/storage/dinkes2.png',
                     '/storage/dinkes3.png',
                ],
                'fields' => [['name' => 'Kesehatan Masyarakat', 'icon' => '🏥']]
            ],
            [
                'name' => "Dinas Sumber Daya Air dan Bina Marga",
                'slug' => 'sumber-daya-air',
                'description' => "DSDABM memiliki dua tugas utama, yakni melakukan pengelolaan terhadap sistem drainase (saluran air), rumah pompa, dan waduk untuk mencegah banjir.",
                'address' => "Jl. Jimerto No. 6-8, Surabaya",
                'phone' => "(031) 5312144",
                'operational_hours' => "08.00 - 16.00",
                'required_education' => "S1 Teknik Sipil/Lingkungan",
                'document_requirements' => ["KTP", "Proposal"],
                'documentation_images' => [
                     '/storage/dinsumberair1.png',
                     '/storage/dinsumberair2.png',
                     '/storage/dinsumberair3.png',
                 ],
                'fields' => [['name' => 'Teknik Sipil', 'icon' => '🏗️']]
            ],
            [
                'name' => "Dinas Ketahanan Pangan dan Pertanian",
                'slug' => 'ketahanan-pangan',
                'description' => "DKPP Kota Surabaya bertanggung jawab atas pengawasan keamanan pangan di pasaran, membina kelompok tani dan peternak kota, serta menjalankan program untuk menjaga stabilitas pasokan pangan.",
                'address' => "Jl. Pagesangan II No. 56, Surabaya",
                'phone' => "(031) 8282328",
                'operational_hours' => "07.30 - 15.30",
                'required_education' => "S1 Pertanian/Peternakan",
                'document_requirements' => ["Surat Pengantar", "CV"],
                'documentation_images' => [
                     '/storage/dinpangan1.png',
                     '/storage/dinpangan2.png',
                     '/storage/dinpangan3.png',
                 ],
                'fields' => [['name' => 'Pertanian', 'icon' => '🌾']]
            ],
            [
                'name' => "Dinas Perumahan Rakyat dan Kawasan Permukiman",
                'slug' => 'perumahan-permukiman',
                'description' => "DPRKPP Surabaya memiliki tugas mengurus segala hal yang berhubungan dengan perumahan, mengelola Rumah Susun Sewa (Rusunawa), menata kawasan kumuh, pemakaman umum.",
                'address' => "Jl. Pemuda No. 15, Surabaya",
                'phone' => "(031) 5343051",
                'operational_hours' => "08.00 - 16.00",
                'required_education' => "S1 Arsitektur/Planologi",
                'document_requirements' => ["KTP", "Portofolio"],
                'documentation_images' => [
                     '/storage/dinperumahan1.png',
                     '/storage/dinperumahan2.png',
                     '/storage/dinperumahan3.png',
                 ],
                'fields' => [['name' => 'Arsitektur', 'icon' => '🏘️']]
            ],
        ];

        foreach ($instansis as $data) {
            $fields = $data['fields'];
            unset($data['fields']);

            // Update or Create OPD
            $opd = Opd::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            // Sync Fields (Delete old, add new to ensure freshness)
            $opd->bidangs()->delete();
            foreach ($fields as $field) {
                $opd->bidangs()->create($field);
            }
        }
    }
}
