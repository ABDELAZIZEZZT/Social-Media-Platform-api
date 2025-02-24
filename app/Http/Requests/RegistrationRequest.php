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
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'job_title'=>'required|max:255',
//            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password'=>['required','confirmed',
                Password::min('8')
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }
}
