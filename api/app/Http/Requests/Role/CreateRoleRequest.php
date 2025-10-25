<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;
use App\Models\Permission;

class CreateRoleRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can("create", Role::class);
    }

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
            "name" => "required|unique:roles",
            "description" => "nullable|string",
            "color" => "nullable|string",
            "permissions" => "nullable|array",
            "permissions.*" => "exists:permissions,id",
        ];
    }
}
