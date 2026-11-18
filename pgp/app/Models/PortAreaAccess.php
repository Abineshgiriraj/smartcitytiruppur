<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortAreaAccess extends Model
 {
    use HasFactory;
    protected $table = 'port_area_access';

    protected $fillable = [
        'id',
        'unique_id',
        'area_name',
        'short_name',
        'description',
        'status',
        // 'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}