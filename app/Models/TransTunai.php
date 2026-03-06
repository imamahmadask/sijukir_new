<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransTunai extends Model
{
    use HasFactory;

    protected $table = 'trans_tunai';

    protected $fillable = [
        'tgl_transaksi',
        'jumlah_transaksi',
        'no_kwitansi',
        'jukir_id',
        'area_id',
        'selisih',
        'keterangan',
        'type',
        'dokumen',
    ];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class, 'jukir_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
