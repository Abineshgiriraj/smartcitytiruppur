<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicle_registration';
    protected $fillable = [
        'unique_id',
        'screenUniqueId',
        'vehicle_type',
        'vehicle_category',
        'Vehicle_image',
        'vech_reg_no',
        'owner_name',
        'contact_number',
        'user_id',
        'password',
        'confirm_password',
        'hash_pass_pin',
        'acc_year',
        'entry_date',
        'rf_id',
        'port',
        'vehicle_serial_no',
        'is_active',
        'is_delete'

    ];

    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }
}
