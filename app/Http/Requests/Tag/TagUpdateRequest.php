<?php

namespace App\Http\Requests\Tag;

use App\Models\Tag;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $entityType = $this->request->get('entityType');
        $tag = $this->route('tag');

        return
            [
                'tag' => [
                    'required',
                    'string',
                    Rule::unique('tag')->where(function (Builder $query) use ($entityType, $tag) {
                        $query->where([
                            ['id', '!=', $tag->getKey()],
                            ['entity_type', 'App\Models\\' . $entityType],
                            ['deleted_at', null],
                        ]);

                        return $query;
                    }),
                ],
                'backgroundColor' => [
                    'string',
                ],
                'textColor' => [
                    'required',
                    'string',
                ],
                'entityType' => [
                    'required',
                    Rule::in(Tag::AVAILABLE_ENTITY),
                ],
            ];
    }
}
