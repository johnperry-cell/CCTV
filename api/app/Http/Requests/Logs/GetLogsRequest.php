<?php

namespace App\Http\Requests\Logs;

use App\Http\Requests\SuperRequest;

class GetLogsRequest extends SuperRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [];
    }
}
