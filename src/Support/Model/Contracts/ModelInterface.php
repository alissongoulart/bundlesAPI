<?php
namespace App\Support\Model\Contracts;

interface ModelInterface
{
    /**
     * @param $params
     * @return Model|array
     */
    public function findAllByAttributes(array $params);

    /**
     * @param $params
     * @return Model|array
     */
    public function findOneByAttributes(array $params);
}