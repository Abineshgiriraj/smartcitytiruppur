<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestingMaster extends Model
{
    use HasFactory;
    protected $table = 'testing_master';

    protected $fillable = [
        'unique_id',
        'test_type',
        'product_group',
        'testing_name',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}