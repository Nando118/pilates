<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'branch',
        'username',
        'gender',
        'phone',
        'address',
        'profile_picture'
    ];

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, "user_id", "id");
    }
}
