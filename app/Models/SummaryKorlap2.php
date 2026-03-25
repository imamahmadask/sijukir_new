<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryKorlap2 extends Model
{
     protected $table = 'summary_korlaps_2';

    protected $fillable = [
        'korlap_id',
        'bulan',
        'tahun',
        'jml_jukir',
        'potensi_harian',
        'potensi_bulanan',
        'kompensasi',
        'pencapaian',
        'ach',
    ];

    public function korlap()
    {
        return $this->belongsTo(Korlap::class);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    public function scopeForMonth($query, $month)
    {
        return $query->where('bulan', $month);
    }

    public function scopeForKorlap($query, $korlapId)
    {
        return $query->where('korlap_id', $korlapId);
    }
}
