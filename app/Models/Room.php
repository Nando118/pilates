<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class, "room_id", "id"); // Satu room dapat memiliki banyak lesson schedules
    }
}
