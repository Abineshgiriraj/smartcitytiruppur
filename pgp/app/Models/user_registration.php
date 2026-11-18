<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_registration extends Model
{
    use HasFactory;
    protected $table = 'user_registration';
    protected $fillable = [
        'acc_year',
        'emp_name',
        'emp_id',
        'designation',
        'district',
        'zone',
        'school',
        'doj',
        'emp_contact',
    ];
}
