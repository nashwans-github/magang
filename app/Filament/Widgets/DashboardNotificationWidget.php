<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\MagangApplication;

class DashboardNotificationWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-notification-widget';

    protected static ?int $sort = -2; // Ensure it appears at the top

    public function mount()
    {
        // Logic to verify if user has an approved application
    }

    public static function canView(): bool
    {
        // Only show for Pemohon or Peserta
        // And only if they have an approved application
        $user = auth()->user();
        if (! in_array($user->role, ['pemohon', 'peserta'])) {
            return false;
        }

        return MagangApplication::where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }
}
