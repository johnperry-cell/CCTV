<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Prop extends AppModel {
    protected $fillable = ["name", "value", "propable_id", "propable_type"];

    public function propable(): MorphTo {
        return $this->morphTo();
    }
}
