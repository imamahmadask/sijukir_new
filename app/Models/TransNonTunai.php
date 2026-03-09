<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransNonTunai extends Model
{
    use HasFactory;

    protected $table = 'trans_non_tunai';

    protected $fillable = [
        'tgl_transaksi',
        'bulan',
        'tahun',
        'merchant_id',
        'merchant_name',
        'issuer_name',
        'total_nilai',
        'area_id',
        'syslog',
        'status',
        'sender_name',
        'kecamatan',
        'filename',
        'info',
        'settlement',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function jukir(){
        return $this->belongsTo(Jukir::class);
    }
}
