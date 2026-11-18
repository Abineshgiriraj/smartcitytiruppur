<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class StandardCreation extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'standard_creation'; // Update table name if needed

    // Define the fillable fields
    protected $fillable = [
        'unique_id',
        'zone',
        'school_id',
        'school_name',
        'school_address',
        'school_taluk',
        'school_district',
        'school_zone',
        'medium',
        'standard',
        'remarks',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];

    // Define the timestamps if needed
    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_delete', 0);
        });
    }
}