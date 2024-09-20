<?php

namespace App\Http\Requests\Dashboard\LessonSchedules;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "date" => ["required", "date", "after_or_equal:today"],
            "time_slot" => ["required", "integer", "exists:time_slots,id"],
            "lesson" => ["required", "integer", "exists:lessons,id"],
            "lesson_type" => ["required", "integer", "exists:lesson_types,id"],
            "coach_user" => ["required", "integer", "exists:users,id"],
            "room" => ["required", "integer", "exists:rooms,id"],
            "quota" => ["required", "numeric", "min:1"]
        ];
    }
}
