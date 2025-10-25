<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    use HasFactory;

    protected $table = 'repair_logs';

    protected $fillable = [
        'camera_id',
        'user_id',
        'is_repaired',
        'repair_start_timestamp',
        'repair_end_timestamp'
    ];

    protected $casts = [
        'is_repaired' => 'boolean',
        'repair_start_timestamp' => 'datetime',
        'repair_end_timestamp' => 'datetime',
    ];

    public function camera() { return $this->belongsTo(Camera::class); }
    public function user() { return $this->belongsTo(User::class); }
}
