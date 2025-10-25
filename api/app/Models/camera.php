<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'location',
        'current_status',
    ];

    /**
     * Get the repair logs for the camera.
     */
    public function repairLogs()
    {
        return $this->hasMany(RepairLog::class);
    }

    /**
     * Get the status history records for the camera.
     */
    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class);
    }
}
