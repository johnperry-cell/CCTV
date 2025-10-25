<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends AppModel {
    use SoftDeletes;

    protected $fillable = ["name", "description"];
    protected $casts = [];

    public function profiles(): HasMany {
        return $this->hasMany(Profile::class);
    }
}
