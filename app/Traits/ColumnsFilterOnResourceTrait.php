<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ColumnsFilterOnResourceTrait
{
    /**
     * Create or Update Group
     *
     * @param  Request $request
     * @param array $resultArray
     * @return array
     */
    protected function filterResourceByColumns(Request $request, array $resultArray): array
    {
        if ($request->has('fields') && !empty($request->has('fields'))) {
            $fields = explode(',', $request->fields);

            if (count($fields) > 0) {
                $resultArrayCustom = array_intersect_key($resultArray, array_flip($fields));

                if (count($resultArrayCustom)) {
                    return $resultArrayCustom;
                }

                return $resultArray;
            }
        }

        return ['resource' => class_basename($this)] + $resultArray;
    }
}
