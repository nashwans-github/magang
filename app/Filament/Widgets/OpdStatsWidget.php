<?php

namespace App\Filament\Widgets;

use App\Models\Bidang;
use App\Models\Peserta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpdStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['admin_pusat', 'admin_opd']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user->role === 'admin_pusat') {

            return [];
        }

        // Existing logic for Admin OPD
        return [
            Stat::make('Selamat Datang', $user->name)
                ->description('Admin OPD')
                ->color('primary'),

            Stat::make('Total Bidang', Bidang::where('opd_id', $user->opd_id)->count())
                ->description('Bidang di OPD Anda')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Total Peserta', Peserta::whereHas('bidang', fn($q) => $q->where('opd_id', $user->opd_id))->count())
                ->description('Peserta magang di OPD Anda')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
