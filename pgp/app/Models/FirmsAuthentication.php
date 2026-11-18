<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmsAuthentication extends Model
{
    use HasFactory;

    protected $table = 'firms_registration';

    protected $fillable = [
        'unique_id',
        'firms',
        'firm_id',
        'company_reg_no',
        'name',
        'mobile',
        'email',
        'contact_person',
        'fax_no',
        'gstin',
        'address_1',
        'address_2',
        'status',
        'status_updated_date',
        'reject_reason',
        'approved_by',
        'is_active',
        'is_delete',
    ];
}
