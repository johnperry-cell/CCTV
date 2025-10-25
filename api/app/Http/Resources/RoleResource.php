<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "id" => $this->hash,
            "name" => $this->name,
            "description" => $this->description,
            "level" => $this->level,
            "color" => $this->color,
            "date" => $this->created_at,
            "protected" => $this->protected,
            "permissions" => PermissionResource::collection($this->permissions),
        ];
    }
}
