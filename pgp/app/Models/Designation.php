<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $table = 'designation';

    protected $fillable = [
        'id',
        'unique_id',
        'designation',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}