<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    protected $fillable = [
        'unique_id',
        'category_id',
        'category_name',
        'group_type',
        'group_id',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}