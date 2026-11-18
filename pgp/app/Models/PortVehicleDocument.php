<?php
// app/Models/VehicleDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortVehicleDocument extends Model
{
    use HasFactory;
    protected $table = 'port_vehicle_registration_sub';
    protected $fillable = [
        // 'vehicle_id',
        'unique_id',
        'screenUniqueId',
        'vehicle_id',
        'certificate_type',
        'certificate_no',
        'valid_up_to',
        'attached_proof',
    ];

    public function vehicle()
    {
        return $this->belongsTo(PortVehicle::class);
    }
}
