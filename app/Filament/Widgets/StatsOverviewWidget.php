<?php

namespace App\Filament\Widgets;

use App\Models\MagangApplication;
use App\Models\Opd;
use App\Models\Peserta;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->role === 'admin_pusat';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Selamat Datang', auth()->user()->name)
                ->description('Admin Pusat')
                ->color('primary'),

            Stat::make('Total Pemohon', MagangApplication::count())
                ->description('Semua permohonan masuk')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Pendaftar', User::whereIn('role', ['pemohon', 'peserta'])->count())
                ->description('Pemohon dan Peserta')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([3, 5, 8, 12, 15, 18, 20]),

            Stat::make('Total OPD Mitra', Opd::count())
                ->description('OPD tujuan magang')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
        ];
    }
}
