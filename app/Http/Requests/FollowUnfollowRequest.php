<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowUnfollowRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
    public function messages() {
        return [
            'user_id.required' => 'User id is required',
            'user_id.integer' => 'User id must be an integer',
            'user_id.exists' => 'User does not exist',
        ];
    }
}
