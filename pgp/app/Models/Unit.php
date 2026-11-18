<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
 {
    use HasFactory;
    protected $table = 'unit';

    protected $fillable = [
        'unique_id',
        'unit_id',
        'unit_name',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}