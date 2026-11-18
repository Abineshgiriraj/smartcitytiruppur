<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class staffRegistration extends Model
{
    use HasFactory;
    protected $table = 'staff_registration';

    protected $fillable = [
        'unique_id',
        'screen_unique_id',
        'name',
        'mobile_no',
        'amobile_no',
        'email_id',
        'user_name',
        'new_password',
        'conf_password',
        'is_active',
        'is_delete',
        'created_at',
        'updated_at'
    ];

    // Define the timestamps if needed
    public $timestamps = true;

    public function documents()
    {
        return $this->hasMany(EmployeeRegistrationDocument::class);
    }

}