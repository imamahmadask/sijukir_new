<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriJukir extends Model
{
    use HasFactory;

    protected $table = 'histori_jukir';
    protected $guarded = ['id'];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class, 'jukir_id');
    }
}
