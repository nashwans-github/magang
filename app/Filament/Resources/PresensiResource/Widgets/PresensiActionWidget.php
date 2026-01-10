<?php

namespace App\Filament\Resources\PresensiResource\Widgets;

use App\Models\Presensi;
use App\Models\Peserta;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Filament\Notifications\Notification;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class PresensiActionWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    
    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.resources.presensi-resource.widgets.presensi-action-widget';

    public static function canView(): bool
    {
        return auth()->user()->role === 'peserta';
    }

    public function checkInAction(): Action
    {
        return Action::make('checkIn')
            ->label('Absen Masuk Sekarang')
            ->color('success')
            ->icon('heroicon-o-arrow-right-end-on-rectangle')
            ->size('lg')
            ->form([
                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                    ])
                    ->default('hadir')
                    ->required()
                    ->live(),
                Textarea::make('notes')
                    ->label('Keterangan')
                    ->placeholder('Isi keterangan jika sakit atau izin...')
                    ->visible(fn (\Filament\Forms\Get $get) => in_array($get('status'), ['sakit', 'izin']))
                    ->required(fn (\Filament\Forms\Get $get) => in_array($get('status'), ['sakit', 'izin'])),
                FileUpload::make('proof_file')
                    ->label('Bukti (Surat Dokter / Izin)')
                    ->directory('presensi-proofs')
                    ->visible(fn (\Filament\Forms\Get $get) => in_array($get('status'), ['sakit', 'izin'])),
            ])
            ->action(function (array $data) {
                $user = auth()->user();
                if ($user->role !== 'peserta') return;

                // Cek Batas Waktu (16:00)
                if (Carbon::now()->format('H:i') >= '16:00') {
                     Notification::make()
                        ->title('Batas waktu presensi (16:00) telah habis.')
                        ->body('Anda dianggap Alpha untuk hari ini.')
                        ->danger()
                        ->send();
                    return;
                }

                $peserta = Peserta::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                if (! $peserta) {
                    Notification::make()->title('Data Peserta tidak ditemukan')->danger()->send();
                    return;
                }

                $today = Carbon::now()->format('Y-m-d');
                
                // Double check DB
                $existing = Presensi::where('peserta_id', $peserta->id)
                    ->whereDate('date', $today)
                    ->first();

                if ($existing) {
                    Notification::make()->title('Sudah absen masuk hari ini')->warning()->send();
                    return;
                }

                try {
                    Presensi::create([
                        'peserta_id' => $peserta->id,
                        'date' => $today,
                        'check_in' => Carbon::now()->format('H:i:s'),
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? null,
                        'proof_file' => $data['proof_file'] ?? null,
                    ]);

                    Notification::make()->title('Berhasil Absen Masuk')->success()->send();
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000) {
                        Notification::make()->title('Sudah absen masuk hari ini')->warning()->send();
                    } else {
                        throw $e;
                    }
                }

                $this->dispatch('presensi-updated');
            });
    }

    public function checkOut()
    {
        $user = auth()->user();
        $peserta = Peserta::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if (! $peserta) return;

        $existing = Presensi::where('peserta_id', $peserta->id)
            ->whereDate('date', Carbon::now()->format('Y-m-d'))
            ->first();

        if (! $existing) return;
        
        if ($existing->check_out) {
            Notification::make()->title('Sudah absen keluar hari ini')->warning()->send();
            return;
        }

        $existing->update([
            'check_out' => Carbon::now()->format('H:i:s'),
        ]);

        Notification::make()->title('Berhasil Absen Keluar')->success()->send();
        
        $this->dispatch('presensi-updated');
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $peserta = null;
        $existingPresensi = null;

        if ($user->role === 'peserta') {
            $peserta = Peserta::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if ($peserta) {
                $existingPresensi = Presensi::where('peserta_id', $peserta->id)
                    ->whereDate('date', Carbon::now()->format('Y-m-d'))
                    ->first();
            }
        }

        return [
            'peserta' => $peserta,
            'existingPresensi' => $existingPresensi,
        ];
    }
}
