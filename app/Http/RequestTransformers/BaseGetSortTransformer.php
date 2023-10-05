<?php

namespace App\Http\RequestTransformers;

class BaseGetSortTransformer extends AbstractRequestTransformer
{
    /**
     * To map fields
     *
     * @return array
     */
    protected function getMap(): array
    {
        return array_merge_recursive(
            parent::paginationParams(),
            parent::sortingParams(),
            [
                'status' => 'status',
            ],
        );
    }

    /**
     * @param array $params
     * @param string $field
     * @return array
     */
    protected function sorting(array $params, string $field): array
    {
        foreach ($this->getMap() as $key => $value) {
            if ($params[$field] === $key) {
                $params[$field] = $value;
            }
        }

        return $params;
    }

    /**
     * @param array $params
     * @return array
     */
    public function map(array $params): array
    {
        if (isset($params['sort'])) {
            $params = $this->sorting($params, 'sort');
        }

        if (isset($params['fields'])) {
            $params = $this->sorting($params, 'fields');
        }

        foreach ($this->getMap() as $key => $value) {
            if ($key === $value) {
                continue;
            }
            if (isset($params[$key])) {
                $params[$value] = $params[$key];
                unset($params[$key]);
            }
        }

        return $params;
    }
}
