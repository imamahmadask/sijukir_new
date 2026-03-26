<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insidentil extends Model
{
    use HasFactory;

    protected $table = 'insidentil';
    protected $guarded = [];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
