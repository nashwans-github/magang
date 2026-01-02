<?php

namespace App\Filament\Widgets;

use App\Models\Peserta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BidangStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->role === 'admin_pembimbing';
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        // Cek apakah user punya relasi pembimbing -> bidang
        $countPeserta = 0;
        
        if ($user->pembimbing && $user->pembimbing->bidang) {
             // Hitung peserta yang ada di bidang pembimbing ini, dan statusnya active
             $bidangId = $user->pembimbing->bidang->id;
             $countPeserta = Peserta::where('bidang_id', $bidangId)
                ->where('status', 'active')
                ->count();
        }

        return [
            Stat::make('Selamat Datang', auth()->user()->name)
                ->description('Pembimbing Lapangan')
                ->color('primary'),

             Stat::make('Peserta Bimbingan', $countPeserta)
                ->description('Peserta Aktif di bidang Anda')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
