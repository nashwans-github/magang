<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagangApplicationMember extends Model
{
    protected $fillable = [
        'magang_application_id',
        'bidang_id',
        'name',
        'jurusan',
        'email',
        'phone',
    ];

    public function application()
    {
        return $this->belongsTo(MagangApplication::class, 'magang_application_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }
}
