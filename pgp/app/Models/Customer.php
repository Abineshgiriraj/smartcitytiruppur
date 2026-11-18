<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    use HasFactory;
    protected $table = 'customer';

    protected $fillable = [
        'unique_id',
        'cus_name',
        'cus_id',
        'gst_no',
        'email',
        'contact_no',
        'contact_name',
        'address',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}
