<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $route = $request->route()?->getName();
        $images = $this->images->sortByDesc("pivot.primary");
        $addresses = $this->addresses->sortByDesc("isMain");
        if (in_array($route, ["auth.permissions", "auth.login"])) {
            $images = $images->where("pivot.primary", true);
            $addresses = $addresses->where("isMain", true);
        }

        return [
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "suffix" => $this->suffix,
            "full_name" => $this->fullName(),
            "nickname" => $this->nickname,
            "birthdate" => $this->birthdate ? Carbon::parse($this->birthdate)->format("Y-m-d") : null,
            "gender" => new GenderResource($this->gender),
            "addresses" => AddressResource::collection($addresses),
            "images" => ProfileImageResource::collection($images),
        ];
    }
}
