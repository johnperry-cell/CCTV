<?php

namespace App\Http\Requests\Permission;

use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\SuperRequest;

class ListPermissionRequest extends SuperRequest {
    protected function failedAuthorization() {
        trail("Permission Management")->alert("Unauthorized access detected.");
        throw new AuthorizationException("Unauthorized access detected.");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [];
    }
}
