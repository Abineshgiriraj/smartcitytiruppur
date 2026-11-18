<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vehicle_complaint_registration extends Model
 {
    use HasFactory;
    protected $table = 'vehicle_complaint_registration';

    protected $fillable = [
    
        'unique_id',
        'complaint_type',
        'person',
        'vehicle_no',
        'user',
        'equipment',
        'reg_no',
        'reg_date',
        'dl_no',
        'name',
        'material',
        'status',
        // 'valid_to',
        // 'contact_number'

    ];
}