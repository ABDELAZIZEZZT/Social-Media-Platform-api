<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'blog_id' => 'required|integer|exists:blogs,id',
            'content' => 'required|string',
            'parent_id'=>'nullable|integer|exists:comments,id'
        ];
    }

    public function messages() {
        return [
            'blog_id.required' => 'Blog id is required',
            'blog_id.integer' => 'Blog id must be an integer',
            'blog_id.exists' => 'Blog does not exist',
            'content.required' => 'Content is required',
            'content.string' => 'Content must be a string',
            'parnt_id.integer' => 'Parent id must be an integer',
            'parnt_id.exists' => 'Parent does not exist',
        ];
    }
}
