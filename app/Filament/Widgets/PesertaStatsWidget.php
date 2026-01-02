<?php

namespace App\Filament\Widgets;

use App\Models\MagangApplication;
use App\Models\Presensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PesertaStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        // Hanya untuk role 'peserta'. Pemohon sekarang pakai ApplicationStatusWidget.
        return in_array(auth()->user()->role, ['peserta']);
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        $stats[] = Stat::make('Selamat Datang', $user->name)
            ->description('Peserta Magang')
            ->color('primary');

        // Status Permohonan Terakhir
        $latestApplication = MagangApplication::where('user_id', $user->id)->latest()->first();
        if ($latestApplication) {
            $color = match($latestApplication->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'warning',
            };
            
            $label = match($latestApplication->status) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => 'Menunggu',
            };

            $stats[] = Stat::make('Status Permohonan', $label)
                ->description($latestApplication->opd->name ?? '-')
                ->color($color);
        } else {
             $stats[] = Stat::make('Status Permohonan', 'Belum Mengajukan')
                ->color('gray');
        }

        // Statistik Kehadiran (Hanya untuk Peserta)
        if ($user->role === 'peserta') {
            // Asumsi relasi user -> peserta -> presensi
            // Atau user id di presensi? Cek migrasi: presensis (user_id/peserta_id?)
            // Migrasi presensi: foreignId('peserta_id')
            // User -> Peserta?
            
            // Hitung kehadiran via relasi peserta
            // Asumsi: User hasOne/hasMany Peserta, Peserta hasMany Presensi
            // Atau jika Presensi punya user_id (cek detail nanti), tapi umumnya via peserta_id
            
            $hadir = Presensi::whereHas('peserta', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'hadir')->count();

            $stats[] = Stat::make('Total Kehadiran', $hadir)
                ->description('Hari masuk')
                ->color('success');
        }

        return $stats;
    }
}
