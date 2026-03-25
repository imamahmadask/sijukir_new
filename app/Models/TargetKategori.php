<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetKategori extends Model
{
    protected $table = "target_kategori";

    protected $fillable = ['tahun', 'bulan', 'kategori', 'sub_kategori', 'target', 'pencapaian', 'selisih', 'persentase'];
}
