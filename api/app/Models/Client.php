<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable {
    use HasApiTokens, HasRoles, SoftDeletes;

    protected $guard_name = "client";

    protected $fillable = ["uuid", "name", "domain", "secret"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [];
    }

    protected $hidden = ["secret"];

    /**
     * Prepare a date for array / JSON serialization.
     */
    // protected function serializeDate(DateTimeInterface $date): string {
    //     return $date->format('Y-m-d');
    // }

    public static function boot() {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid7();
        });
    }
}
