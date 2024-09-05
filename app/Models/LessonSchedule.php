<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'quota'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function waitlists()
    {
        return $this->hasMany(Waitlist::class);
    }
}
