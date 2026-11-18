<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiclecreation extends Model
{
    use HasFactory;
    protected $table = 'vehicle_creation';
    protected $fillable = [
        'unique_id',
        'cargo',
        'vehicle_type',
        'is_active',
        'is_delete'

    ];
}