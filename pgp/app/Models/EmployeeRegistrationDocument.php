<?php
// app/Models/VehicleDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRegistrationDocument extends Model
{
    use HasFactory;
    protected $table = 'employee_reg_doc_sub';
    protected $fillable = [
        'id',
        'unique_id',
        'screenUniqueId',
        'proof_type',
        'proof_no',
        'proof_name',
        'lic_exp_date',
        'is_active',
        'is_delete'
    ];

    public function vehicle()
    {
        return $this->belongsTo(EmployeeRegistration::class);
    }
}
