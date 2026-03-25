<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryJukirMonth extends Model
{
    protected $table = 'summary_jukir_month';
    protected $guarded = ['id'];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }
}
