<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'Kecamatan',
        'kode',
    ];

    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
}
