<?php

namespace App\Observers;

use App\Models\MagangApplication;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class MagangApplicationObserver
{
    /**
     * Handle the MagangApplication "updated" event.
     */
    public function updated(MagangApplication $magangApplication): void
    {
        if ($magangApplication->isDirty('status') && $magangApplication->status === 'approved') {
            
            DB::beginTransaction();
            try {
                // 1. Process Main Applicant (Ketua/Individu)
                $mainUser = User::find($magangApplication->user_id);
                if ($mainUser && $mainUser->role !== 'admin_pusat') {
                    $mainUser->update(['role' => 'peserta']);
                }

                $this->createPesertaIfNotExists(
                    $magangApplication->user_id,
                    $magangApplication->id,
                    $magangApplication->bidang_id,
                    $magangApplication->institution_name // Use institution as major placeholder if needed, or '-'
                );

                // 2. Process Members
                foreach ($magangApplication->members as $member) {
                    // Create or Get User
                    $user = User::firstOrCreate(
                        ['email' => $member->email],
                        [
                            'name' => $member->name,
                            'password' => Hash::make('password123'),
                            'role' => 'peserta',
                        ]
                    );

                    // Ensure role is peserta
                    if ($user->role !== 'peserta' && $user->role !== 'admin_pusat') { // Don't downgrade admins
                         $user->update(['role' => 'peserta']);
                    }

                    $this->createPesertaIfNotExists(
                        $user->id,
                        $magangApplication->id,
                        $member->bidang_id ?? $magangApplication->bidang_id,
                        '-'
                    );
                }
                
                DB::commit();

                Notification::make()
                    ->title('Peserta Berhasil Dibuat')
                    ->body('Akun peserta untuk ketua dan anggota telah digenerate.')
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                DB::rollBack();
                Notification::make()
                    ->title('Gagal Generate Peserta')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

    protected function createPesertaIfNotExists($userId, $applicationId, $bidangId, $major = '-')
    {
        $exists = Peserta::where('magang_application_id', $applicationId)
            ->where('user_id', $userId)
            ->exists();

        if (! $exists) {
            Peserta::create([
                'user_id' => $userId,
                'magang_application_id' => $applicationId,
                'bidang_id' => $bidangId,
                'major' => $major ?? '-',
                'student_id_number' => '-',
                'status' => 'active',
            ]);
        }
    }
}
