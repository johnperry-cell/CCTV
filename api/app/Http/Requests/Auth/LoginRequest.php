<?php

namespace App\Http\Requests\Auth;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;

class LoginRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $fromFrontend = EnsureFrontendRequestsAreStateful::fromFrontend($this);
        $rules = [
            "email" => "required",
            "password" => ["required", PasswordRule::defaults()],
        ];

        if ($fromFrontend) {
            $rules["remember"] = "boolean";
        } else {
            $rules["device_name"] = "required";
        }

        return $rules;
    }
}
