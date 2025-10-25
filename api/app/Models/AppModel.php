<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Traits\PaginatesTrait;

class AppModel extends Model {
    use HasFactory, HashableId, PaginatesTrait;

    public static function getTableName() {
        return with(new static())->getTable();
    }
}
