<?php

namespace App\Http\Requests\Dashboard\CoachCertifications;

use Illuminate\Foundation\Http\FormRequest;

class CreateCoachCertificationRequest extends FormRequest
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
            "name" => ["required", "string"],
            "certification_names.0" => ["required", "string", "min:3", "max:50"], // Field pertama harus diisi
            "certification_names.*" => ["nullable", "string", "min:3", "max:50"]  // Field lainnya validasi hanya jika diisi
        ];
    }
}
