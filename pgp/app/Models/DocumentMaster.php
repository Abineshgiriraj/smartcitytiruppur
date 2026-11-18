<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class DocumentMaster extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'document_master'; // Update table name if needed

    // Define the fillable fields
    protected $fillable = [
        'unique_id',
        'category_type',
        'type_of_proof',
        'is_delete',
        'is_active',
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