<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    use HasFactory;
    protected $table = 'branch';

    protected $fillable = [
        'unique_id',
        'branch_name',
        'address',
        'city',
        'district',
        'state',
        'pincode',
        'gst_no',
        'email',
        'contact_no',
        'contact_name',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at',

    ];
}
