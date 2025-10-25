<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource {
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
            "date" => Carbon::parse($this->created_at)->format("Y-m-d"),
        ];
    }
}
