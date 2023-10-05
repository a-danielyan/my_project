<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginateHelper
{
    /**
     * Respond with a paginator response.
     *
     * @param LengthAwarePaginator $result
     * @return array
     */
    public function meta(LengthAwarePaginator $result): array
    {
        return [
            'links' => [
                'path' => $result->toArray()['path'],
                'firstPageUrl' => $result->toArray()['first_page_url'],
                'lastPageUrl' => $result->url($result->lastPage()),
                'nextPageUrl' => $result->nextPageUrl(),
                'prevPageUrl' => $result->previousPageUrl(),
            ],
            'meta' => [
                'currentPage' => $result->currentPage(),
                'from' => $result->firstItem(),
                'lastPage' => $result->lastPage(),
                'perPage' => $result->perPage(),
                'to' => $result->lastItem(),
                'total' => $result->total(),
                'count' => $result->count(),
            ],
        ];
    }
}
