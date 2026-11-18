<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessGate extends Model
{
    use HasFactory;
    protected $table = 'access_gate';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'unique_id',
        'access_gate',
        'short_name',
        'description',
        'status',
    ];
}