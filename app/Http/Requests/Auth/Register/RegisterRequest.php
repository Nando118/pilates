<?php

namespace App\Http\Requests\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "min:3", "max:200"],            
            "gender" => ["required", "string"],
            "phone" => ["required", "numeric", "min_digits:10", "max_digits:15"],
            "address" => ["nullable", "string", "min:3", "max:200"],
            "email" => ["required", "email:dns", "max:200", "unique:users,email"],
            "profile_picture" => ["nullable", "image", "mimes:jpeg,png,jpg,gif", "max:2048"],
            "password" => ["required", "string", "min:8", "confirmed"]
        ];
    }
}
