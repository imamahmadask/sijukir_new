<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KurangSetor extends Model
{
    protected $table = "kurang_setors";
    protected $fillable = [
        'jukir_id',
        'tahun',
        'tgl_setor',
        'jumlah',
        'histori_jukir_id'
    ];

    public function jukir(){
        return $this->belongsTo(Jukir::class);
    }

    public function historiJukir(){
        return $this->belongsTo(HistoriJukir::class);
    }
}
