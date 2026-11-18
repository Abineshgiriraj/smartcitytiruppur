<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRegistration extends Model
{
    use HasFactory;
    protected $table = 'employee_registration';

    protected $fillable = [
        'unique_id',
        'screenUniqueId',
        'fullname',
        'employee_id',
        'dob',
        'blood_group',
        'aadhar_no',
        'email_id',
        'department',
        'designation',
        'lic_exp_date',
        'area',
        'rfid_no',
        'register_no',
        'reg_date',
        'authority',
        'mobile',
        'address',
        'status',
        'reject_reason',
        'approved_by',
        'approved_date',
        'user_id',
        'pass_pin',
        'password',
        'confirm_password',
        'hash_pass_pin',
        'acc_year',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];

    // Define the timestamps if needed
    public $timestamps = true;

    public function documents()
    {
        return $this->hasMany(EmployeeRegistrationDocument::class);
    }

}