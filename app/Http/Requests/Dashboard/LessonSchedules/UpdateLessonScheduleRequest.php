<?php

namespace App\Http\Requests\Dashboard\LessonSchedules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            "id" => ["required", "string"],
            "date" => ["required", "date", "after_or_equal:today"],
            "time_slot" => ["required", "integer", "exists:time_slots,id"],
            "lesson" => ["required", "integer", "exists:lessons,id"],
            "lesson_type" => ["required", "integer", "exists:lesson_types,id"],
            "coach_user" => ["required", "integer", "exists:users,id"],
            "quota" => ["required", "numeric"], // Tetapkan tanpa `min:1` terlebih dahulu
            "credit_price" => ["required", "numeric", "min:1"]
        ];
    }

    protected function withValidator(Validator $validator)
    {
        // Ambil lesson schedule dari route untuk mendapatkan quota saat ini
        $lessonSchedule = $this->route('lessonSchedule');

        // Tambahkan aturan dinamis pada 'quota' hanya jika nilainya berbeda
        $validator->sometimes('quota', 'min:1', function ($input) use ($lessonSchedule) {
            return $input->quota != $lessonSchedule->quota;
        });
    }
}
