<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortVehicle extends Model
{
    use HasFactory;
    protected $table = 'port_vehicle_registration';
    protected $fillable = [
        'unique_id',
        'screenUniqueId',
        'vech_reg_no',
        'vehicle_id',
        'owner_name',
        'contact_number',
        'vehicle_type',
        'entry_date',
        'vehicle_type',
        'vehicle_category',
        'user_id',
        'acc_year',
        'password',
        'confirm_pass',
        'hash_password',
        'rf_id',
        'is_active',
        'is_delete'

    ];

    public function documents()
    {
        return $this->hasMany(PortVehicleDocument::class);
    }
}
