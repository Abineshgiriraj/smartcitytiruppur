<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_creation extends Model
{
    use HasFactory;

    protected $table = 'user_creation';

    protected $fillable = [
        'unique_id',
        'staff_name',
        'user_name',
        'password',
        'user_type',
        'mobile_number',
        'emaill_id',
        'is_delete',
        'is_active',
        'remeber_token',
        'remarks',
        'status'
    ];
}
