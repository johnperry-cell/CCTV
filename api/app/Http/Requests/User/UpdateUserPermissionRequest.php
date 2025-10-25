<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Role;
use App\Models\Permission;

class UpdateUserPermissionRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can("updatePermission", $this->user);
    }

    public function prepareForValidation() {
        $this->merge([
            "roles" => collect($this->input("roles", []))
                ->map(function ($role) {
                    return Role::hashToId($role);
                })
                ->toArray(),
        ]);

        if ($this->user()->isSuperman()) {
            $this->merge([
                "permissions" => collect($this->input("permissions", []))
                    ->map(function ($permission) {
                        return Permission::hashToId($permission);
                    })
                    ->toArray(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $rules = [
            "roles" => "required|array|min:1",
            "roles.*" => [
                "required",
                "exists:roles,id",
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);
                    if ($role->name == config("mitd.superman") && !$this->user()->isSuperman()) {
                        $fail("Invalid role");
                    }
                },
            ],
        ];
        if ($this->user()->isSuperman()) {
            return array_merge($rules, [
                "permissions" => "nullable|array|min:0",
                "permissions.*" => "exists:permissions,id",
            ]);
        }
        return $rules;
    }
}
