<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLabel extends Model
{
    use HasFactory;

    protected $table = 'status_labels';

    protected $fillable = [
        'descriptions',
        'new_status',
        'camera_name',
        'is_critical',
        'requires_replacement',
        'warranty_implication'
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'requires_replacement' => 'boolean',
    ];

    /**
     * Get the status history record associated with the status label.
     */
    public function statusHistory()
    {
        return $this->hasOne(StatusHistory::class);
    }
}
