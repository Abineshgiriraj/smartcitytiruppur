<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class passentrysub extends Model
{
    use HasFactory;
    protected $table = 'pass_entry_sublist';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'unique_id',
        'screen_unique_id',
        'agent_name',
        'agent_type',
        'type_of_pass',
        'no_of_days',
        'from_date',
        'to_date',
        'amount',
        'gross_amount',
        'sgst',
        'cgst',
        'access_area',
        // 'pass_amnt',
        'is_active',
        'is_delete'
    ];
}