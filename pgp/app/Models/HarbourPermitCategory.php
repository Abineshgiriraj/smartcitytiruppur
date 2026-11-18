<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarbourPermitCategory extends Model
 {
    use HasFactory;
    protected $table = 'harbour_permit_pategory';

    protected $fillable = [
        'acc_year',
        'department',
        'description'
    ];
}