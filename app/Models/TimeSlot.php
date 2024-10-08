<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['start_time', 'end_time', 'duration'];

    public function schedules(): HasMany
    {
        return $this->hasMany(LessonSchedule::class,"time_slot_id", "id");
    }
}
