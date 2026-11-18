<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vehicle_registration extends Model
{
    use HasFactory;
    protected $table = 'vehicle_registration';

    protected $fillable = [
        'unique_id',
        'vech_reg_no',
        'vehicle_serial_no',
        'owner_name',
        'contact_number',
        'status',
        'user_id',
        'approval_status',
        'reject_reason',
        'approved_date',
        'approved_by',
        'entry_date'

    ];
}
