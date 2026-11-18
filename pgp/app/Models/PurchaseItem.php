<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    protected $table = 'purchase_item';

    protected $fillable = [

        'unique_id',
        'item_name',
        'hsn_code',
        'product_group_id',
        'product_category_id',
        'description',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'

    ];
}