<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class online_vehicle_registration extends Model
 {
    use HasFactory;
    protected $table = 'online_vehicle_registration_creation';

    protected $fillable = [
        'vehicle_registration_no',
        'vehicle_type',
        'Owner’s_name',
        'insurance_details',
        'fitness_certificate',
        'puc',
        'Document Upload',
        'valid_from',
        'valid_to',
        'contact_number'

    ];
}