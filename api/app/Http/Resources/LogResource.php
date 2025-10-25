<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "id" => $this->hash,
            "user" => $this->user
                ? [
                    "username" => $this->user->username,
                    "email" => $this->user->email,
                    "full_name" => $this->user->profile?->fullName(),
                ]
                : null,
            "actor" => $this->actor,
            "action" => $this->action,
            "type" => $this->type,
            "level" => $this->level,
            "module" => $this->module,
            "old_data" => json_decode($this->old_data ?? ""),
            "new_data" => json_decode($this->new_data ?? ""),
            "timestamp" => Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
        ];
    }
}
