<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagangApplication extends Model
{
    protected $fillable = [
        'user_id',
        'opd_id',
        'bidang_id',
        'institution_name',
        'jurusan',
        'start_date',
        'end_date',
        'status',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }

    public function members()
    {
        return $this->hasMany(MagangApplicationMember::class);
    }

    protected static function booted(): void
    {
        static::updated(function (MagangApplication $application) {
            if ($application->wasChanged('status') && $application->status === 'approved') {
                
                \Illuminate\Support\Facades\DB::transaction(function () use ($application) {
                    // 1. Create Peserta for Main Applicant (Ketua/Individu)
                    // Check if already exists to prevent duplicate
                    $exists = \App\Models\Peserta::where('magang_application_id', $application->id)
                        ->where('user_id', $application->user_id)
                        ->exists();

                    if (! $exists) {
                        \App\Models\Peserta::create([
                            'user_id' => $application->user_id,
                            'magang_application_id' => $application->id,
                            'bidang_id' => $application->bidang_id, // Main applicant's bidang
                            'major' => '-', // Placeholder as per user request (optional)
                            'student_id_number' => '-', // Placeholder
                            'status' => 'active',
                        ]);
                    }

                    // 2. Process Members
                    foreach ($application->members as $member) {
                        // Find or Create User for Member
                        $user = \App\Models\User::firstOrCreate(
                            ['email' => $member->email],
                            [
                                'name' => $member->name,
                                'password' => \Illuminate\Support\Facades\Hash::make('password123'), // Default password
                                'role' => 'peserta',
                            ]
                        );

                        // Ensure user has peserat role if they existed before
                        // (Optional: $user->update(['role' => 'peserta']) if you want to force)

                        // Create Peserta for Member
                        $memberExists = \App\Models\Peserta::where('magang_application_id', $application->id)
                            ->where('user_id', $user->id)
                            ->exists();
                        
                        if (! $memberExists) {
                            \App\Models\Peserta::create([
                                'user_id' => $user->id,
                                'magang_application_id' => $application->id,
                                'bidang_id' => $member->bidang_id ?? $application->bidang_id, // Use member's bidang, or fallback to main (though migration made member bidang nullable, schema should ensure it)
                                'major' => '-',
                                'student_id_number' => '-',
                                'status' => 'active',
                            ]);
                        }
                    }
                });
            }
        });
    }
}
