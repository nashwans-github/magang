<?php

namespace App\Filament\Widgets;

use App\Models\Opd;
use Filament\Widgets\ChartWidget;

class OpdApplicationsChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Pendaftar per Dinas';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin_pusat';
    }

    protected function getData(): array
    {
        $data = Opd::withCount(['magangApplications', 'pesertas'])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemohon (Pelamar)',
                    'data' => $data->pluck('magang_applications_count'),
                    'backgroundColor' => '#3b82f6', // blue-500
                    'borderColor' => '#2563eb', // blue-600
                ],
                [
                    'label' => 'Jumlah Pendaftar (Peserta Aktif)',
                    'data' => $data->pluck('pesertas_count'),
                    'backgroundColor' => '#10b981', // emerald-500
                    'borderColor' => '#059669', // emerald-600
                ],
            ],
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
