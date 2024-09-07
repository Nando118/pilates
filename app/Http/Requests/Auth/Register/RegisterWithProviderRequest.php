<?php

namespace App\Http\Requests\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

class RegisterWithProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return session()->has('PROVIDER_ID');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // "branch" => ["required", "string"],
            "username" => ["required", "alpha_dash", "min:3", "max:50", "unique:user_profiles,username"],
            "gender" => ["required", "string"],
            "phone" => ["required", "numeric", "min_digits:10", "max_digits:15"],
            "address" => ["required", "string", "min:3", "max:200"],
            "profile_picture" => ["nullable", "image", "mimes:jpeg,png,jpg,gif", "max:2048"]
        ];
    }
}
