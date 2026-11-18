<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_permission extends Model
{
    use HasFactory;

    protected $table = "user_screen_permission";

    protected $fillable = [

        'unique_id',
        'user_type',
        'main_screen_id',
        'screen_unique_id',
        'action_unique_id',
        'is_active',
        'is_delete'
    ];
}
