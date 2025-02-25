<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'job_title' => 'required|string|max:255',
            'password' => [
                'required',
                'confirmed',
                Password::min('8')
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
        ];
    }
    public function messages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'first_name.max' => 'The first name must not exceed 255 characters.',

            'last_name.required' => 'The last name field is required.',
            'last_name.max' => 'The last name must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'This email is already taken you can login.',

            'job_title.required' => 'The job title field is required.',
            'job_title.max' => 'The job title must not exceed 255 characters.',

            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.mixedCase' => 'The password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character.',
        ];
    }
}
