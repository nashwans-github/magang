<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = [
        'peserta_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'proof_file',
        'notes',
        'is_approved',
    ];

    protected $casts = [
        'date' => 'date',
        'is_approved' => 'boolean',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
