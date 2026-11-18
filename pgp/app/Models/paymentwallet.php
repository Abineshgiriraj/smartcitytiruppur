<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentwallet extends Model
{
    use HasFactory;
    protected $table = 'staff';

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