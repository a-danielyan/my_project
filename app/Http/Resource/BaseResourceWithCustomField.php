<?php

namespace App\Http\Resource;

use App\Models\BaseModelWithCustomFields;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BaseModelWithCustomFields
 */
class BaseResourceWithCustomField extends JsonResource
{
    protected array $customFieldList = [];

    public function __construct($resource, $params = null, $customFieldList = [])
    {
        parent::__construct($resource);
        $this->customFieldList = $customFieldList;
    }


    protected function getCustomFieldValues(): Collection
    {
        if ($this->resource === null) {
            return new Collection();
        }
        if (!$this->relationLoaded('customFields')) {
            return new Collection();
        }

        if (!empty($this->customFieldList)) {
            $customFieldValues = $this->customFields()->with(['customField'])
                ->whereHas(
                    'customField',
                    function ($query) {
                        $query->whereIn('code', $this->customFieldList);
                    },
                )->get();
        } else {
            $customFieldValues = $this->customFields;
        }

        return $customFieldValues;
    }


    protected function getCustomFields(Collection $customFieldValues): Collection|\Illuminate\Support\Collection
    {
        $availableCustomFields = $customFieldValues->mapWithKeys(function ($item) {
            if ($item->customField) {
                return [$item->customField->code => $item->text_value];
            }

            return [];
        });

        foreach ($this->customFieldList as $field) {
            if (!$availableCustomFields->has($field)) {
                $availableCustomFields[$field] = null;
            }
        }

        return $availableCustomFields;
    }
}
