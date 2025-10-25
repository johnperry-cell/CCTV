<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Traits\PaginatesTrait;
use App\Traits\HasTeam;

class Role extends SpatieRole {
    use HasFactory, HashableId, PaginatesTrait, HasTeam;

    protected $casts = [
        "protected" => "boolean",
    ];
}
