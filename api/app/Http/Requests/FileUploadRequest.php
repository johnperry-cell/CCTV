<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            "uid" => "nullable|string",
            "name" => "required_without:uid",
            "rename" => "required_without:uid",
            "size" => "required_without:uid|numeric|min:1",
        ];
    }

    public function messages(): array
    {
        return [
            "uid.string" => "Invalid upload id.",
            "name.required_without" => "A file name is required.",
            "rename.required_without" => "A file name is required.",
            "size.required_without" => "A file size is required.",
            "size.min" => "A file size must be greater than 0.",
        ];
    }
}
