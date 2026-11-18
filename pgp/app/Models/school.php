<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $table = 'schools';

    protected $fillable = [
        'unique_id',
        'zone_name',
        'district_name',
        'taluk_name',
        'school_name',
        'remark',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}