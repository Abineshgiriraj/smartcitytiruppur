<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCreation extends Model {
    use HasFactory;

    protected $table = 'company_creation';
    // Ensure this matches your database table name

    protected $fillable = [
        'unique_id',
        'company_name',
        'address',
        'city',
        'district',
        'state',
        'pincode',
        'gst_no',
        'email',
        'contact_name',
        'contact_no',
        'created',
        'pan',
        'bank_name',
        'bank_branch',
        'branch_name',
        'acc_no',
        'ifsc',
        'web',
        'logo',
        'short_logo',
        'is_active',
        'is_delete',
    ];

}
