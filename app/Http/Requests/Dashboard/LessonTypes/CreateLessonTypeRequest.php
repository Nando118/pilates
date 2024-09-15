<?php

namespace App\Http\Requests\Dashboard\LessonTypes;

use Illuminate\Foundation\Http\FormRequest;

class CreateLessonTypeRequest extends FormRequest
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
            "name" => ["required", "string", "min:3", "max:25", "unique:lesson_types,name"],
            "quota" => ["required", "numeric", "min:1"]
        ];
    }
}
