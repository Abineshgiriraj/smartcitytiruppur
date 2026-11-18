<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class person_registration extends Model
{
    use HasFactory;
    protected $table = 'person_registration';

    protected $fillable = [
        'unique_id',
        'form_unique_id',
        'academic_year',
        'full_name',
        'register_no',
        'reg_date',
        'address',
        'designation',
        'licence_validity_date',
        'aadhaar_no',
        'nationality',
        'country',
        'mobile',
        'blood_group',
        'email_id',
        'photo_pic',
        'image_upload',
        'user_id',
        'rf_id',
        'status',
        'reject_reason',
        'approved_date',
        'approved_by',
        'authenication_type',
        'password',
        'confirm_password',
        'port_type',
        'pin_num',
        'confirm_pin',
        'is_active',
        'is_delete'

    ];
}
