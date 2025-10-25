<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Traits\PaginatesTrait;

class Permission extends SpatiePermission {
    use HasFactory, HashableId, PaginatesTrait;
}
