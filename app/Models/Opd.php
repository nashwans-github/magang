<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'operational_hours',
        'required_education',
        'document_requirements',
        'description',
        'documentation_images',
    ];

    protected $casts = [
        'documentation_images' => 'array',
        'document_requirements' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bidangs()
    {
        return $this->hasMany(Bidang::class);
    }

    public function magangApplications()
    {
        return $this->hasMany(MagangApplication::class);
    }

    public function beritas()
    {
        return $this->hasMany(Berita::class);
    }

    public function pesertas()
    {
        return $this->hasManyThrough(Peserta::class, Bidang::class);
    }
}
