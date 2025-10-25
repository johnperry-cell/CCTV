<?php

namespace App\Traits;

use Closure;

use App\MITD\Paginates;
trait PaginatesTrait {
    public static function paginates(
        int $limit = 25,
        ?int $page = null,
        ?Closure $closure = null,
        ?Closure $searchClosure = null,
        ?Closure $countClosure = null
    ) {
        return Paginates::paginates(self::query(), $limit, $page, $closure, $searchClosure, $countClosure);
    }
}
