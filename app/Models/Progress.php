<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progresses';

    protected $fillable = [
        'peserta_id',
        'date',
        'title',
        'description',
        'file_path',
        'status',
        'feedback',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
