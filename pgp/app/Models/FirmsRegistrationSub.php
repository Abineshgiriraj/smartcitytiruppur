<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmsRegistrationSub extends Model
{
    use HasFactory;
    protected $table = 'firms_registration_sub';
    protected $fillable = [
        'unique_id',
        'form_unique_id',
        'proof_type',
        'aadhar_licence',
        'attached_proof',
    ];
}