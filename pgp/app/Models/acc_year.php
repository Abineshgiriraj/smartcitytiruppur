<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class acc_year extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unique_id',
        'from_year',
        'to_year',
        'acc_year',
        'remarks',
        'status',
        'created_at',
        'updated_at'
    ];

    // In User model
    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('is_delete', 0);
        });
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // In Post model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
