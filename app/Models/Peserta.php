<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
        'user_id',
        'magang_application_id',
        'bidang_id',
        'major',
        'student_id_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magangApplication()
    {
        return $this->belongsTo(MagangApplication::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class);
    }

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class);
    }

    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}
