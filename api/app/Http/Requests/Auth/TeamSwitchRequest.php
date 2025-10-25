<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TeamSwitchRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return config("permission.teams") && $this->user()->isSuperman();
    }

    public function prepareForValidation() {
        if (config("permission.teams")) {
            $team_id = app(config("mitd.permission.teams_provider"))->hashToId($this->input("team_id"));
            $this->merge(["team_id" => $team_id]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $table_name = app(config("mitd.permission.teams_provider"))->getTable();
        return [
            "team_id" => ["required", "exists:" . $table_name . ",id"],
        ];
    }

    public function messages(): array {
        return [
            "team_id.required" => "Team is required.",
            "team_id.exists" => "Team does not exist.",
        ];
    }
}
