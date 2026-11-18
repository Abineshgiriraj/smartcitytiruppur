<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpare extends Model
{
    use HasFactory;

    protected $table = 'product_spare';

    protected $fillable = [
        'unique_id',
        'product_group',
        'spare_group',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}
