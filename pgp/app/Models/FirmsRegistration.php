<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmsRegistration extends Model
{
    use HasFactory;

    protected $table = 'firms_registration';

    protected $fillable = [
        'unique_id',
        'form_unique_id',
        'academic_year',
        'firms',
        'firm_id',
        'company_reg_no',
        'name',
        'aadhar_no',
        'mobile',
        'email',
        'gstin',
        'address',
        'pan_no',
        'proprietor_name',
        'adhaar_1',
        'proprietor_name_1',
        'email_1',
        'otp',
        'mobile_1',
        'proprietor_name_2',
        'adhaar_2',
        'email_2',
        'mobile_2',
        'login_id',
        'password',
        'confirm_password',
        'entry_date',
        'is_active',
        'is_delete',
        'port_status',
    ];
}
