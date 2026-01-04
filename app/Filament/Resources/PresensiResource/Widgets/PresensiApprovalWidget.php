<?php

namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Models\Presensi;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class PresensiApprovalWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.resources.presensi-resource.widgets.presensi-approval-widget';
    
    protected int | string | array $columnSpan = 'full';

    public function mount()
    {
        // Auto refresh logic if needed
    }

    public static function canView(): bool
    {
        // Show only if NOT peserta (Admin/Pembimbing)
        return auth()->user()->role !== 'peserta';
    }

    public function getViewData(): array
    {
        // Get all pending approvals ordered by date desc
        $approvals = Presensi::with(['peserta.user'])
            ->where('is_approved', false)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'approvals' => $approvals,
        ];
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Setujui')
            ->color('success')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                // We will pass the ID via arguments manually in blade or use specific method per row
                // But simplified: Direct method call is easier for simple cards
            });
    }

    public function approve($id)
    {
        $presensi = Presensi::find($id);
        if ($presensi) {
            $presensi->update(['is_approved' => true]);
            Notification::make()->title('Presensi Disetujui')->success()->send();
            
            // Dispatch event to update table if needed
            $this->dispatch('presensi-updated'); 
        }
    }
    
    public function reject($id)
    {
         $presensi = Presensi::find($id);
         if ($presensi) {
             // Maybe delete or set status to rejected?
             // For now just delete or keep as unapproved?
             // User asked "setujui", implies binary choice. 
             // Let's assume reject deletes the invalid record or marks it.
             // Given schema has no "rejected" status, maybe just delete?
             // Or leave it pending.
             // I'll implement approve only for now as requested.
         }
    }
}
