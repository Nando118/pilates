<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    // RELATIONS
    public function users(): BelongsToMany
    {
        // Penghubung User dengan Role lewat tabel pivot user_roles
        return $this->belongsToMany(User::class, "user_roles");
    }
}
