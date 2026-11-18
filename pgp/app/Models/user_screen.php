<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_screen extends Model
{
    use HasFactory;

    protected $table ='user_screens';

    protected $fillable = [
        'unique_id',
        'main_screen',
        'sub_screen',
        'sub_screen_icon',
        'screen_name',
        'folder_name',
        'order_no',
        'icon',
        'remarks',
        'action',
        'is_active',
        'is_delete'
    ];

}