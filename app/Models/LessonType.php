<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["name", "quota"];

    public function lessonSchedules(): HasMany
    {
        return $this->hasMany(LessonSchedule::class,"lesson_type_id", "id");
    }
}
