<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryByArea extends Model
{
    protected $table = 'summary_by_area';
    protected $guarded = ['id'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
