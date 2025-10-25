<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UpdateUserPasswordRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $route = $this->route();
        if ($route->getName() == "users.password.update") {
            return $this->user()->can("updateAccount", $this->user);
        } elseif ($route->getName() == "auth.password.update") {
            return $this->user()->can("updateOwnPassword", $this->user());
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "password" => ["required", PasswordRule::defaults()],
        ];
    }
}
