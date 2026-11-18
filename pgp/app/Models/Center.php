<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;
    protected $table = 'centers';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'unique_id',
        'center_name',
        'short_name',
        'description',
        'status',
    ];
}