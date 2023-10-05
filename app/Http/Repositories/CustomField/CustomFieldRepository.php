<?php

namespace App\Http\Repositories\CustomField;

use App\Http\Repositories\BaseRepository;
use App\Models\CustomField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomFieldRepository extends BaseRepository
{
    protected CustomFieldOptionRepository $customFieldOptionRepository;

    public function __construct(
        CustomFieldOptionRepository $customFieldOptionRepository,
        CustomField $customField,
    ) {
        $this->customFieldOptionRepository = $customFieldOptionRepository;
        $this->model = $customField;
    }

    public function create(array $data): Model
    {
        $options = $data['options'] ?? [];

        /** @var CustomField $model */
        $model = $this->model->newQuery()->create($data);

        if (in_array($model->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
            $sortOrder = 1;

            foreach ($options as $optionInputs) {
                $this->customFieldOptionRepository->create(
                    array_merge([
                        'custom_field_id' => $model->id,
                        'sort_order' => $sortOrder++,
                    ], $optionInputs),
                );
            }
        }

        return $model;
    }


    public function update(Model $model, array $data = [], bool $isTrashed = false): bool
    {
        /** @var CustomField $model */
        if ($isTrashed && method_exists($model, 'withTrashed')) {
            $model->withTrashed();
        }

        $model->update($data);

        $previousOptionIds = $model->options()->pluck('id');


        if (in_array($model->type, ['select', 'multiselect', 'checkbox']) && isset($data['options'])) {
            $sortOrder = 1;

            foreach ($data['options'] as $optionId => $optionInputs) {
                if (Str::contains($optionId, 'option_')) {
                    $this->customFieldOptionRepository->create(
                        array_merge([
                            'attribute_id' => $model->id,
                            'sort_order' => $sortOrder++,
                        ], $optionInputs),
                    );
                } else {
                    if (is_numeric($index = $previousOptionIds->search($optionId))) {
                        $previousOptionIds->forget($index);
                    }
                    /*
                     * //@todo check update method
                     *     $this->customFieldOptionRepository->update(
                            array_merge([
                                'sort_order' => $sortOrder++,
                            ], $optionInputs),
                            $optionId,
                        );
                        */
                }
            }
        }

        foreach ($previousOptionIds as $optionId) {
            $this->customFieldOptionRepository->delete($optionId);
        }

        return true;
    }
}
