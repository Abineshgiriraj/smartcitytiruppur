<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gateaccess extends Model
{
    use HasFactory;
    protected $table = 'gate_access_details';

    protected $fillable = [
        'unique_id',
        'rfid',
        'pass_sub_id',
        'gate_id',
        'in_out_datetime'
    ];
}