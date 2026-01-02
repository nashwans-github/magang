<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $fillable = [
        'opd_id',
        'name',
        'description',
        'icon',
        'logo',
    ];

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function pembimbings()
    {
        return $this->hasMany(Pembimbing::class);
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }
}
