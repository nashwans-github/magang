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
            // Admin Pusat sees a card for each OPD with applicant count
            $stats = [];
            $opds = \App\Models\Opd::withCount('magangApplications')->get();

            foreach ($opds as $opd) {
                $stats[] = Stat::make($opd->name, $opd->magang_applications_count)
                    ->description('Jumlah Pemohon')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('primary');
            }

            // Also show total Applicants overall as key summary?
            // User requested "tiap opd jadi buatkan cardnya", so the loop is key.
            return $stats;
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
