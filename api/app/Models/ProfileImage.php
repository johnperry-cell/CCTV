<?php

namespace App\Models;

class ProfileImage extends AppModel {
    protected $fillable = ["profile_id", "image_id", "primary"];

    protected $casts = ["primary" => "boolean"];
}
