<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complaint_type extends Model
 {
    use HasFactory;

    protected $table = 'complaint_type';

    protected $fillable = [
        'unique_id',
        'complaint_type',
        'status',
        'is_active',
        'is_delete'
    ];
}