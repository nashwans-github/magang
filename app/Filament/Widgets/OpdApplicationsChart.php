<?php

namespace App\Filament\Widgets;

use App\Models\Opd;
use Filament\Widgets\ChartWidget;

class OpdApplicationsChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Pemohon per Dinas';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin_pusat';
    }

    protected function getData(): array
    {
        $data = Opd::withCount('magangApplications')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pemohon',
                    'data' => $data->pluck('magang_applications_count'),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
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
