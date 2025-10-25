<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\SuperRequest;

class CreateClientRequest extends SuperRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $routeName = $this->route()->getName();
        $isEdit = $routeName == "client.update";
        return [
            "name" => ["required", "string", "max:255", "unique:clients,name," . ($isEdit ? $this->client : "null") . ",uuid"],
            "domain" => ["required", "string", "max:255", "unique:clients,domain," . ($isEdit ? $this->client : "null") . ",uuid"],
        ];
    }
}
