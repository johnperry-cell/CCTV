<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserUsernameRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $route = $this->route();
        if ($route->getName() == "users.username.update") {
            return $this->user()->can("updateAccount", $this->user);
        } elseif ($route->getName() == "auth.username.update") {
            return $this->user()->can("updateOwnAccount", $this->user());
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $id = $this->user()->id;
        $route = $this->route();
        if ($route->getName() == "users.username.update") {
            $id = $this->user->id;
        }
        return [
            "username" => [
                "required",
                "unique:users,username," . $id,
                /**
                 * Username can only contain letters, numbers, and the following characters: . _ -
                 * Username must start with a letter or number
                 */
                "regex:/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/",
            ],
        ];
    }

    public function messages(): array {
        return [
            "username.required" => "Username is required.",
            "username.unique" => "Username is already taken.",
            "username.regex" => "Invalid Username format.",
        ];
    }
}
