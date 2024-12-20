<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'registration_type',
        'credit_balance',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // RELATIONS
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, "user_id", "id");
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, "user_id", "id");
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, "user_roles");
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where("name", $roleName)->exists();
    }

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class,"user_id", "id");
    }

    // Jika ingin memfilter hanya untuk role 'coach'
    public function isCoach()
    {
        return $this->roles()->where("name", "coach")->exists();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, "user_id", "id");
    }

    // Relasi ke tabel certifications
    public function coachCertifications(): HasMany
    {
        return $this->hasMany(CoachCertification::class, "user_id", "id");
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class, 'user_id', 'id');
    }

    public function hasSufficientCredit(int $requiredCredit): bool
    {
        return $this->credit_balance >= $requiredCredit;
    }
}
