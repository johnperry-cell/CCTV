<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\SuperRequest;
use App\Models\Permission;

class UpdatePermissionRequest extends SuperRequest {
    public function prepareForValidation() {
        $this->merge([
            "permissions" => collect($this->input("permissions", []))
                ->map(function ($permission) {
                    return Permission::hashToId($permission);
                })
                ->toArray(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "permissions" => "nullable|array",
            "permissions.*" => "exists:permissions,id",
        ];
    }
}
