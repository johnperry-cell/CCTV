<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "client_id" => $this->uuid,
            "client_name" => $this->name,
            "client_domain" => $this->domain,
            "active" => !$this->trashed(),
            "permissions" => PermissionResource::collection($this->getDirectPermissions()),
        ];
    }
}
