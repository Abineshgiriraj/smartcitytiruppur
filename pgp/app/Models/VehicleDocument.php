<?php
// app/Models/VehicleDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasFactory;
    protected $table = 'vehicle_registration_sub';
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
        return $this->belongsTo(Vehicle::class);
    }
}
