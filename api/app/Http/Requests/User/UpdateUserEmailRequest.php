<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserEmailRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $route = $this->route();
        if ($route->getName() == "users.email.update") {
            return $this->user()->can("updateAccount", $this->user);
        } elseif ($route->getName() == "auth.email.update") {
            return $this->user()->can("updateOwnAccount", $this->user());
        }
        return false;
    }

    public function prepareForValidation(): void {
        $this->merge([
            "email" => strtolower($this->input("email")),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $id = $this->user()->id;
        $route = $this->route();
        if ($route->getName() == "users.email.update") {
            $id = $this->user->id;
        }
        return [
            "email" => ["required", "email:rfc,dns", Rule::unique("users", "email")->ignore($id)],
        ];
    }
}
