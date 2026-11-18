<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portcategory extends Model
{
    use HasFactory;

    protected $table = 'port_category';

    protected $fillable = [
        'unique_id',
        'category',
        'sub_category',
        'description',
        'is_active',
        'is_delete'
    ];
}
