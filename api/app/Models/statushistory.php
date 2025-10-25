<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    use HasFactory;

    protected $table = 'status_histories';

    protected $fillable = [
        'camera_id',
        'user_id',
        'status_label_id',
        'reason',
        'status_change_timestamp',
        'new_status',
        'urgency'
    ];

    protected $casts = [
        'status_change_timestamp' => 'datetime',
    ];

    public function camera() { return $this->belongsTo(Camera::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function statusLabel() { return $this->belongsTo(StatusLabel::class); }
}
