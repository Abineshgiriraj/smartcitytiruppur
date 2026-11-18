<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class main_user_screen extends Model
{
    use HasFactory;

    protected $table = 'main_user_screens';

    protected $fillable = [
        'unique_id',
        'screen_type',
        'screen_name',
        'order_no',
        'icon',
        'remarks',
        'is_active',
        'is_delete'
    ];
}
