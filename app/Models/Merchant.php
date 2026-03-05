<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $table = 'merchant';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'merchant_name',
        'vendor',
        'nmid',
        'no_rekening',
        'tgl_terdaftar',
        'qris',
        'area_id',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function jukir()
    {
        return $this->hasOne(Jukir::class);
    }
}
