<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCreation extends Model
{
    use HasFactory;

    protected $table = 'product_creation';

    protected $fillable = [
        'unique_id',
        'product_id',
        'product_name',
        'product_category',
        'product_group',
        'unit',
        'hsn_code',
        'warranty_period',
        'description',
        'is_active',
        'is_delete',
    ];
}
