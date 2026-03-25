<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryMonth extends Model
{
    protected $table = "summary_by_month";

    protected $fillable = [
        'bulan', 'tahun', 'tunai', 'jml_trx', 'non_tunai', 'berlangganan', 'total', 'max_createdAt',
    ];
}
