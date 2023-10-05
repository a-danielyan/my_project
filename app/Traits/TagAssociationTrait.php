<?php

namespace App\Traits;

use App\Models\Tag;
use App\Models\TagEntityAssociation;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait TagAssociationTrait
{
    public function tag(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                Tag::class,
                TagEntityAssociation::class,
                'entity_id',
                'id',
                'id',
                'tag_id',
            )->select([
                'tag.id',
                'tag.tag',
                'background_color',
                'text_color',
            ]);
    }

    public function tagsAssociation(): MorphMany
    {
        return $this->morphMany(TagEntityAssociation::class, 'entityRecord', 'entity', 'entity_id');
    }
}
