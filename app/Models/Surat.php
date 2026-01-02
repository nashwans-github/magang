<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $fillable = [
        'peserta_id',
        'type',
        'file_path',
        'issued_date',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
