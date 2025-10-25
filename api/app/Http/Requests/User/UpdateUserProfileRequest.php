<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Gender;

class UpdateUserProfileRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $route = $this->route();
        if ($route->getName() == "users.profile.update") {
            return $this->user()->can("updateProfile", $this->user);
        } elseif ($route->getName() == "auth.profile.update") {
            return $this->user()->can("updateOwnProfile", $this->user());
        } elseif ($route->getName() == "auth.profile.update.forced") {
            return !$this->user()->profile?->fullName();
        }
        return false;
    }

    public function prepareForValidation(): void {
        $this->merge([
            "gender" => Gender::hashToId($this->input("gender")),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            "first_name" => "required",
            "last_name" => "required",
            "middle_name" => "nullable",
            "nickname" => "nullable",
            "suffix" => "nullable",
            "gender" => "required|exists:genders,id",
            "birthdate" => "required|date|date_format:Y-m-d|before:tomorrow",
        ];
    }

    public function messages(): array {
        return [
            "birthdate.before" => "Unfortunately, time travelers are not permitted.",
        ];
    }
}
