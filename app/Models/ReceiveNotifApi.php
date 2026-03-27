<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveNotifApi extends Model
{
    protected $table = 'receive_notif_api';
    protected $fillable = [
        'syslog',
        'tgl_transaksi',
        'merchant_id',
        'merchant_name',
        'jumlah',
        'issuer_name',
        'status',
        'pesan_notif',
        'tgl_notif',
        'sender_name',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function jukir()
    {
        return $this->belongsTo(Jukir::class, 'merchant_id', 'merchant_id');
    }
}
