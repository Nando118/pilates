<?php

namespace App\Http\Requests\Dashboard\CoachCertifications;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoachCertificationRequest extends FormRequest
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
            "name" => ["required", "string"],
            "certification_name" => ["required", "string", "min:3", "max:50"],
            "date" => ["required", "date"],
            "organization_name" => ["required", "string", "min:3", "max:50"]
        ];
    }
}
