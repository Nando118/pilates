<?php

namespace App\Http\Requests\Dashboard\UserCredits;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserCreditsRequest extends FormRequest
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
            "type" => "required|in:add,deduct",
            "credit_balance" => "required|integer|min:1"
        ];
    }

    public function messages()
    {
        return [
            "type.required" => "Transaction type is required.",
            "type.in" => "Invalid transaction type.",
            "credit_balance.required" => "Credit balance is required.",
            "credit_balance.integer" => "Credit balance must be a number.",
            "credit_balance.min" => "Credit balance must be at least 1."
        ];
    }

}
