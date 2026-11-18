<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class cargoEiRegistration extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'cargo_ei_registration'; // Update table name if needed

    // Define the fillable fields
    protected $fillable = [
        'unique_id',
        'entry_date',
        'transaction_type',
        'exp_imp_no',
        'exp_imp_name',
        'address',
        'cargo_name',
        'package_qty',
        'total_weight',
        'vessel_name',
        'vessel_sr_no',
        'agent_type',
        'agent_details',
        'org_destination',
        'is_delete',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Define the timestamps if needed
    public $timestamps = true;

    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_delete', 0);
        });
    }
}