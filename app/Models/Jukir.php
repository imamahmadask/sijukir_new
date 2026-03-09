<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jukir extends Model
{
    protected $table = 'jukirs';

    protected $fillable = [        
        'kode_jukir',
        'nik_jukir',
        'nama_jukir',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'kel_alamat',
        'kec_alamat',
        'telepon',
        'agama',
        'jenis_jukir',
        'status',
        'foto',
        'lokasi_id',
        'document',
        'merchant_id',
        'jenis_kelamin',
        'no_perjanjian',
        'tgl_perjanjian',
        'tgl_akhir_perjanjian',
        'potensi_harian',
        'ket_jukir',
        'tgl_terbit_qr',
        'hari_libur',
        'jml_hari_kerja',
        'waktu_kerja',
        'area_id',
        'hari_kerja_bulan',
        'kab_kota_alamat',
        'uji_petik',
        'tgl_pkh_upl',
        'potensi_bulanan',
        'potensi_bulanan_upl',
        'tgl_nonactive',
        'old_merchant_id',
        'is_migration'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }    

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function histori()
    {
        return $this->hasMany(HistoriJukir::class, 'jukir_id');
    }

    public function pembantu()
    {
        return $this->hasMany(PembantuJukir::class, 'jukir_id');
    }
}
