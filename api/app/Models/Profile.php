<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Profile extends AppModel {
    protected $fillable = ["user_id", "gender_id", "first_name", "middle_name", "last_name", "suffix", "nickname", "birthdate"];

    protected $casts = [
        "birthdate" => "date",
    ];

    public function user(): HasOne {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function gender(): HasOne {
        return $this->hasOne(Gender::class, "id", "gender_id");
    }

    public function addresses(): MorphMany {
        return $this->morphMany(Address::class, "addressable");
    }

    public function images(): BelongsToMany {
        return $this->belongsToMany(Image::class, "profile_images", "profile_id", "image_id")->withPivot("primary");
    }

    public function setPrimaryImage(Image $image) {
        $this->images()
            ->newPivotStatement()
            ->where("profile_id", "=", $this->id)
            ->update(["primary" => false]);
        $this->images()->updateExistingPivot($image->id, ["primary" => true]);
        return $this;
    }

    public function fullName(): string|null {
        return $this->buildFullName($this->first_name, $this->middle_name, $this->last_name, $this->suffix);
    }

    private function buildFullName($first_name = null, $middle_name = null, $last_name = null, $suffix = null): string|null {
        $fullName = null;
        if ($first_name) {
            $fullName .= $first_name;
        }
        if ($middle_name) {
            $fullName .= " " . substr($middle_name, 0, 1) . ".";
        }
        if ($last_name) {
            $fullName .= " " . $last_name;
        }
        if ($suffix) {
            $fullName .= " " . $suffix;
        }

        return $fullName ? ucwords(strtolower($fullName)) : null;
    }
}
