<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAddressRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $route = $this->route();
        if (in_array($route->getName(), ["users.address.create", "users.address.update"])) {
            return $this->user()->can("updateProfile", $this->user);
        } elseif (in_array($route->getName(), ["auth.address.create", "auth.address.update"])) {
            return $this->user()->can("updateOwnProfile", $this->user());
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
            "location" => "required",
            "zipCode" => "required|numeric|digits_between:4,5",
            "barangay" => "required|exists:barangays,code",
        ];
    }
}
