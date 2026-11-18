<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person_reg_sublist extends Model
{
    use HasFactory;
    protected $table = 'person_reg_sublist';

    protected $fillable = [
        'unique_id',
        'form_unique_id',
        'proof_type',
        'aadhar_licence',
        'attached_proof',
        'expiry_date',
        'is_active',
        'is_delete'
    ];
}