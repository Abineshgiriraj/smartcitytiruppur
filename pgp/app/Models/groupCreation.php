<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groupCreation extends Model {
    use HasFactory;
    protected $table = 'group_creation';

    protected $fillable = [
        'unique_id',
        'group_id',
        'group_name',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}
