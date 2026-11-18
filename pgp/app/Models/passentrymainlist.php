<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class passentrymainlist extends Model
 {
    use HasFactory;
    protected $table = 'pass_entry_main_list';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'unique_id',
        'screen_unique_id',
        'issue_center',
        'firms_agent',
        'agent_type',
        'vectype',
        'pass_type',
        'persontype',
        'vec_reg_no',
        'adhaar_no',
        'driving_license',
        'access_area',
        'adhaar_no',
        'vech_no',
        'pass_weight',
        'pass_amount',
        'cover_letter',
        'is_active',
        'is_delete',
        'pass_id',
        'pass_status',
        'net_amount',
        'acadamic_year'
    ];
}