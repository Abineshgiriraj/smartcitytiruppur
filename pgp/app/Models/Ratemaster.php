<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratemaster extends Model
{
    use HasFactory;

    protected $table = 'rate_master';

    protected $fillable = [
        'unique_id',
        'access_gate',
        'allowed_type',
        'vehicle_person_type',
        'validity',
        'rate',
        'penalty_per_hour',
        'penalty_per_day',
        'approval',
        'approval_status',
        'reject_reason',
        'approval_date',
        'approved_by',
        'entry_date',
        'is_active',
        'is_delete'
    ];
}
