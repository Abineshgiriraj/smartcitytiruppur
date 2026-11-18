<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class biometric_attandance extends Model
{
    use HasFactory;

    protected $table = 'biometric_detail';

    public function getFormattedPunchingDatetimeAttribute()
    {
        return Carbon::parse($this->punching_datetime)->format('Y-m-d h:i A');
    }

}