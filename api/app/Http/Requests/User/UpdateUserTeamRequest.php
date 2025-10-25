<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserTeamRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return config("permission.teams") && $this->user()->isSuperman();
    }

    public function prepareForValidation() {
        $this->merge([
            "team" => app(config("mitd.permission.teams_provider"))::hashToId($this->input("team")),
            "roles" => collect($this->input("roles"))->map(fn($role) => Role::hashToId($role))->toArray(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $teamsTable = app(config("mitd.permission.teams_provider"))::getTableName();
        return [
            "team" => ["required", "exists:$teamsTable,id"],
            "roles" => ["required", "array"],
            "roles.*" => [
                function ($attribute, $value, $fail) {
                    if (Role::where("id", $value)->where("team_id", $this->input("team"))->count() == 0) {
                        $fail("Invalid role");
                    }
                },
            ],
        ];
    }
}
