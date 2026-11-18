<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
 {
    use HasFactory;
    protected $table = 'supplier';

    protected $fillable = [
        'unique_id',
        'supplier_id',
        'supplier_name',
        'contact',
        'email',
        'gstin',
        'bank_name',
        'account_no',
        'ifsc_code',
        'address',
        'pincode',
        'delivery_address',
        'delivery_pincode',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}