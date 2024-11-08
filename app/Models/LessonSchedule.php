<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["date", "lesson_code", "time_slot_id", "lesson_id", "lesson_type_id", "user_id", "quota", "credit_price"];

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, "time_slot_id", "id");
    }

    // Metode untuk memeriksa ketersediaan time slot
    public static function isTimeSlotAvailable($date, $timeSlotId)
    {
        // Cek jika ada lesson_schedule dengan waktu dan ruangan yang sama
        return !self::where('date', $date)
            ->where('time_slot_id', $timeSlotId)
            ->exists();
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, "lesson_id", "id");
    }

    public function lessonType(): BelongsTo
    {
        return $this->belongsTo(LessonType::class, "lesson_type_id", "id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, "lesson_schedule_id", "id");
    }
}
