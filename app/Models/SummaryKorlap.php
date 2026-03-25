<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryKorlap extends Model
{
    protected $table = 'summary_korlaps';
    
    protected $fillable = [
        'korlap_id',
        'bulan',
        'tahun',
        'jml_jukir',
        'hijau',
        'kuning',
        'merah',
        'ach_hijau',
        'ach_kuning',
        'ach_merah',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'jml_jukir' => 'integer',
        'hijau' => 'integer',
        'kuning' => 'integer',
        'merah' => 'integer',
        'ach_hijau' => 'float',
        'ach_kuning' => 'float',
        'ach_merah' => 'float',
    ];

    public function korlap()
    {
        return $this->belongsTo(Korlap::class);
    }

    public function area()
    {
        return $this->belongsToMany(Area::class, 'lokasis', 'korlap_id', 'area_id', 'korlap_id')
            ->distinct();
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

    public function getTotalJukirAttribute()
    {
        return $this->hijau + $this->kuning + $this->merah;
    }
}
