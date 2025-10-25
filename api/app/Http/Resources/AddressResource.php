<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "id" => $this->hash,
            "type" => $this->type
                ? [
                    "id" => $this->type->hash,
                    "name" => $this->type->name,
                ]
                : null,
            "full" => $this->fullAddress(),
            "location" => $this->location,
            "zipCode" => $this->zipCode,
            "isMain" => $this->isMain,
            "barangay" => [
                "code" => $this->barangay->code,
                "name" => $this->barangay->name,
            ],
            "city" => [
                "code" => $this->city->code,
                "name" => $this->city->name,
            ],
            "province" => [
                "code" => $this->province->code,
                "name" => $this->province->name,
            ],
            "region" => [
                "code" => $this->region->code,
                "name" => $this->region->name,
            ],
            "islandGroup" => [
                "code" => $this->islandGroup->code,
                "name" => $this->islandGroup->name,
            ],
        ];
    }

    private function fullAddress() {
        $address = "";
        $address .= $this->location . ", ";
        $address .= $this->barangay->name . ", ";
        $address .= $this->city->name . ", ";
        $address .= $this->province->name . ", ";
        $address .= $this->region->name . " " . $this->zipCode;
        return $address;
    }
}
