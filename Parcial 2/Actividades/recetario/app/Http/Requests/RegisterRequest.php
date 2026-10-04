<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash:ascii', Rule::unique('users', 'name')],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }
}
