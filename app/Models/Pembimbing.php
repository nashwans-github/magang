<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembimbing extends Model
{
    protected $fillable = [
        'user_id',
        'bidang_id',
        'nip',
        'position',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }
}
