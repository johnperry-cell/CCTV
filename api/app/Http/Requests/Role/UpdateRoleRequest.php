<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;
use App\Models\Permission;

class UpdateRoleRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can("update", $this->role);
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
        $rules = [
            "description" => "nullable|string",
            "color" => "nullable|string",
            "permissions" => "nullable|array",
            "permissions.*" => "exists:permissions,id",
        ];
        $nameRule = $this->role->protected
            ? []
            : ["name" => "required|unique:roles,name," . $this->role->id];
        return [...$nameRule, ...$rules];
    }
}
