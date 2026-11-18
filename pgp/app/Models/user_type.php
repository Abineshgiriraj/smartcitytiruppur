<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_type extends Model
{
    use HasFactory;

    protected $table = 'user_types';

    protected $fillable = [
        'unique_id',
        'user_type',
        'status',
        'is_active',
        'is_delete'
    ];
}
