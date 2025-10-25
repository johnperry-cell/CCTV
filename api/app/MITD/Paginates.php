<?php

namespace App\MITD;

use Closure;

class Paginates {
    public static function paginates(
        $query,
        int $limit,
        ?int $page = null,
        ?Closure $closure = null,
        ?Closure $searchClosure = null,
        ?Closure $countClosure = null
    ) {
        $page = max(1, $page ?? 1);
        $limit = max(1, (int) $limit);
        $lastPage = 0;
        $total = 0;
        $emit = false;

        while (!$emit) {
            $count =
                $query
                    ->clone()
                    ->reorder()
                    ->when(!!$closure, $closure)
                    ->when(!!$countClosure, $countClosure)
                    ->selectRaw("count(*) as count")
                    ->first()?->count ?? 0;

            $lastPage = ceil($count / $limit);
            if ($page <= $lastPage) {
                $total = $count;
                $emit = true;
            } else {
                $page = $lastPage;
            }
        }

        $query = $query
            ->clone()
            ->when(!!$closure, $closure)
            ->when(!!$searchClosure, $searchClosure)
            ->limit($limit)
            ->offset(($page - 1) * $limit);

        return [
            "data" => $query->get(),
            "total" => $total,
            "page" => $page,
            "pages" => $lastPage,
            "limit" => $limit,
        ];
    }
}
