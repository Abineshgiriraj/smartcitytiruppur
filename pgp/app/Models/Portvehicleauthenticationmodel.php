<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portvehicleauthenticationmodel extends Model
{
    use HasFactory;

    protected $table = 'port_vehicle_registration';

    protected $fillable = [
        'vech_reg_no',
        'vehicle_type',
        'entry_date',
        'vehicle_id',
        'status',
        'status_updated_date',
        'reject_reason',
        'approved_by',
        
    ];
}
