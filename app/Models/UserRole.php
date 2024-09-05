<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Pivot
{
    use SoftDeletes;

    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
