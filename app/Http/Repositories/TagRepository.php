<?php

namespace App\Http\Repositories;

use App\Models\Tag;

class TagRepository extends BaseRepository
{
    /**
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }
}
