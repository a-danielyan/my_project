<?php

namespace App\DTO;

use App\Exceptions\CustomErrorException;

class DataSet
{
    private array $data;

    /**
     * DataSet constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $property
     * @param bool $required
     * @return mixed
     * @throws CustomErrorException
     */
    public function get($property, bool $required = true): mixed
    {
        if (!isset($this->data[$property])) {
            if ($required) {
                throw new CustomErrorException('Unknown property ' . $property);
            }

            return null;
        }

        return $this->data[$property];
    }
}
