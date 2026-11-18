<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonAuthentication extends Model
 {
    use HasFactory;
    protected $table = 'person_authentication';

    protected $fillable = [
        'acc_year',
        'department',
        'description'
    ];
}