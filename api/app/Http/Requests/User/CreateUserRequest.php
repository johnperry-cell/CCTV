<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;

use App\Models\Role;
use App\Models\User;

class CreateUserRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can("create", User::class);
    }

    public function prepareForValidation() {
        $this->merge([
            "roles" => collect($this->input("roles", []))
                ->map(function ($role) {
                    return Role::hashToId($role);
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
            "email" => "nullable|email:rfc,dns|unique:users,email",
            "username" => "required|unique:users,username",
            "password" => ["required", PasswordRule::defaults()],
            "roles" => "required|array|min:1",
            "roles.*" => "required|exists:roles,id",
        ];
    }
}
