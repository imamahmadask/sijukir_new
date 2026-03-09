<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembantuJukir extends Model
{
    use HasFactory;

    protected $table = 'jukir_pembantu';
    protected $guarded = ['id'];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class, 'jukir_id');
    }
}
