<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class Taluk extends Model
{
    use HasFactory;
    protected $table = 'taluk'; // Update table name if needed


    protected $fillable = [
        'unique_id',
        'zone_name',
        'district_name',
        'taluk_name',
        'remark',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_delete', 0);
        });
    }
}