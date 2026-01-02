<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = [
        'peserta_id',
        'pembimbing_id',
        'attendance_score',
        'discipline_score',
        'task_completion_score',
        'deadline_accuracy_score',
        'independence_score',
        'final_score',
        'comments',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }
}
