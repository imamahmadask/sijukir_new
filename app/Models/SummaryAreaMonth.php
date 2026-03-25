<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryAreaMonth extends Model
{
    protected $table = 'summary_area_month';
    protected $guarded = ['id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
