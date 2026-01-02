<?php

namespace App\Filament\Widgets;

use App\Models\MagangApplication;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ApplicationStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.application-status-widget';
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Hanya terlihat oleh pemohon. Peserta mungkin sudah punya dashboard sendiri (Grafik dll).
        // User minta "dashboard pemohon", jadi kita batasi ke pemohon.
        return Auth::user()->role === 'pemohon';
    }

    public function getViewData(): array
    {
        return [
            'application' => MagangApplication::where('user_id', Auth::id())->latest()->first(),
        ];
    }
}
