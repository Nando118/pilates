<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterWithNormalFormRequest extends FormRequest
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
            "username" => ["required", "string", "min:3", "max:50", "unique:users,username"],
            "gender" => ["required", "string"],
            "phone" => ["required", "string"],
            "address" => ["required", "string", "min:3", "max:200"],
            "email" => ["required", "email:dns", "max:200", "unique:users,email"],
            "password" => ["required", "string", "min:8", "confirmed"],
            'password_confirmation' => ["required", "string", "min:8"]
        ];
    }
}
