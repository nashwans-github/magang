<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peserta;
use App\Models\Presensi;
use Carbon\Carbon;

class AutoAlphaPresensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:auto-alpha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Secara otomatis menandai peserta sebagai Alpha jika belum absen hingga pukul 16:00';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Ambil semua peserta aktif
        $activePesertas = Peserta::where('status', 'active')->get();
        
        $count = 0;

        foreach ($activePesertas as $peserta) {
            // Cek apakah sudah presensi hari ini
            $hasPresensi = Presensi::where('peserta_id', $peserta->id)
                ->whereDate('date', $today)
                ->exists();

            if (! $hasPresensi) {
                // Buat presensi Alpha
                Presensi::create([
                    'peserta_id' => $peserta->id,
                    'date' => $today,
                    'status' => 'alpa',
                    'notes' => 'Otomatis oleh sistem (Tidak absen hingga 16:00)',
                    // check_in null, check_out null
                ]);
                $count++;
            }
        }

        $this->info("Berhasil memproses presensi otomatis. {$count} peserta ditandai Alpha.");
    }
}
