<?php

namespace App\Http\Requests\Permission;

use App\Http\Requests\SuperRequest;

class StorePermissionRequest extends SuperRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "name" => ["required", "unique:permissions,name" /*, "not_regex:/[^:a-z_-]{1,}/"*/],
            "description" => "nullable|string",
        ];
    }

    public function messages(): array {
        return [
            "name.required" => "The permission name is required",
            "name.unique" => "The permission name already exists",
            // "name.not_regex" => "The permission name is invalid",
        ];
    }
}
