<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'unique_id',
        'emis_no',
        'biometric_id',
        'device_id',
        'school_id',
        'udise_code',
        'student_name',
        'dob',
        'cur_std',
        'group_name',
        'class_sec',
        'medium',
        'schl_name',
        'schl_block',
        'schl_district',
        'father_name',
        'mother_name',
        'is_delete',
    ];
}