<?php

namespace App\Http\Requests\Dashboard\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingsRequest extends FormRequest
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
        $rules = [
            "id" => "required", "string",
            "name.0" => "required", "string", // Field name pertama harus diisi
        ];

        // Tambahkan validasi untuk setiap participant yang diinput
        for ($i = 1; $i < count($this->name); $i++) {
            $rules["name." . $i] = "nullable|string"; // Field selanjutnya tidak wajib diisi
        }

        return $rules;
    }

    public function messages()
    {
        return [
            "name.0.required" => "The first name field must be filled in.",
            "name.*.string" => "Name must be text.",
        ];
    }

}
