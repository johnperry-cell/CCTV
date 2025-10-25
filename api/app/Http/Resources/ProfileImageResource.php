<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileImageResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $urls = [
            "main" => route("image.display.private", ["image" => $this->hash]),
        ];
        $sizes = config("mitd.images.sizes.thumbnails");

        foreach ($sizes as $key => $size) {
            $urls[$key] = route("image.display.private", [
                "image" => $this->hash,
                "size" => $key,
            ]);
        }

        return [
            "id" => $this->hash,
            "alt" => $this->alt,
            "primary" => $this->pivot->primary,
            "mime" => $this->file->mime,
            "size" => $this->file->size,
            "ext" => $this->file->ext,
            "url" => $urls,
        ];
    }
}
