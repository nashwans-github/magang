<?php

namespace App\Filament\Resources\MagangApplicationResource\Pages;

use App\Filament\Resources\MagangApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMagangApplication extends EditRecord
{
    protected static string $resource = MagangApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        
        if ($record->status === 'approved') {
             \Filament\Notifications\Notification::make()
                ->title('Permohonan Disetujui')
                ->body('Akun peserta telah dibuat/diupdate. Password default untuk anggota baru adalah "password123".')
                ->success()
                ->persistent() // Agar tidak cepat hilang
                ->send();
        }
    }
}
