<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\MITD\Logger;

class Log extends AppModel {
    protected $fillable = ["user_id", "actor", "action", "type", "level", "old_data", "new_data", "module"];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
