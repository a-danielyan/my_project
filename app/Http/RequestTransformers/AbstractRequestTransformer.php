<?php

namespace App\Http\RequestTransformers;

use Illuminate\Http\Request;

/**
 * Class AbstractRequestTransformer
 * @package App\Http\RequestTransformers
 */
abstract class AbstractRequestTransformer
{
    /**
     * Abstract function to get data for mapping
     *
     * @return array
     */
    abstract protected function getMap(): array;

    /**
     * Function to get json data for mapping
     *
     * @return array
     */
    protected function getJsonMap(): array
    {
        return [];
    }

    /**
     * To transform request data
     *
     * @param Request $request
     * @return array
     */
    public function transform(Request $request): array
    {
        $transformed = [];
        $requestArray = $request->all();

        foreach ($requestArray as $key => $value) {
            if (isset($this->getMap()[$key])) {
                $transformed[$this->getMap()[$key]] = $value;
            } elseif (!empty($this->getJsonMap()) && isset($this->getJsonMap()[$key])) {
                $transformed[$this->getJsonMap()[$key]] = json_decode($value, true);
            }
        }

        return $transformed;
    }

    /**
     * @return array
     */
    protected static function paginationParams(): array
    {
        return [
            'limit' => 'limit',
            'page' => 'page',
            'fields' => 'fields',
            'distinct' => 'distinct',
            'exact' => 'exact',
        ];
    }

    /**
     * @return array
     */
    protected static function sortingParams(): array
    {
        return [
            'sort' => 'sort',
            'order' => 'order',
        ];
    }
}
