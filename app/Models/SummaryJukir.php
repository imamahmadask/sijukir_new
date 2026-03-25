<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryJukir extends Model
{
    protected $table = 'summary_jukir';
    protected $guarded = ['id'];

    public function jukir()
    {
        return $this->belongsTo(Jukir::class);
    }
}
