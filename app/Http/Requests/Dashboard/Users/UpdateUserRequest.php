<?php

namespace App\Http\Requests\Dashboard\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "role" => ["required", "string"],
            // "branch" => ["required", "string"],
            "name" => ["required", "string", "min:3", "max:200"],
            "username" => ["required", "alpha_dash", "min:3", "max:50"],
            "gender" => ["required", "string"],
            "phone" => ["required", "numeric", "min_digits:10", "max_digits:15"],
            "address" => ["nullable", "string", "min:3", "max:200"],
            "email" => ["required", "email:dns", "max:200"],
            "profile_picture" => ["nullable", "image", "mimes:jpeg,png,jpg,gif", "max:2048"]
        ];
    }
}
