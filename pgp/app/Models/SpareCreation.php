<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpareCreation extends Model
{
    protected $table = 'spare_creation';

    protected $fillable = [
        'unique_id',
        'spare_id',
        'spare_name',
        'spare_group',
        'spare_category',
        'unit',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',
    ];

}
