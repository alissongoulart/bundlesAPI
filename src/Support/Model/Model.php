<?php

namespace App\Support\Model;

use App\Support\Model\Contracts\ModelInterface;

abstract class Model implements ModelInterface
{
    private $persistence = 'diagram.json';
    public $attributes = [];

    /**
     * @param $params
     * @return array
     */
    public function findAllByAttributes(array $params)
    {
        $allModels = $this->getAllModels();
        return $this->checkParams($allModels, $params);
    }

    /**
     * @param $params
     * @return Model|array
     */
    public function findOneByAttributes(array $params)
    {
        $allModels = $this->getAllModels();
        $models = $this->checkParams($allModels, $params);

        if (count($models) > 0) {
            $values = array_values($models);
            $attributes = array_shift($values);
            return $this->newModelInstance($attributes);
        }

        return $models;
    }

    /**
     * @param $attributes
     * @return Model
     */
    protected function newModelInstance($attributes)
    {
        $model = clone $this;
        $model->setAttributes($attributes);
        return $model;
    }

    /**
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $index => $attribute) {
            $this->{$index} = $attribute;
        }
    }

    /**
     * @param bool $index
     * @param bool $createModelInstance
     * @return array
     */
    protected function getAllModels($index = false, $createModelInstance = true)
    {
        $models = [];
        if (!$index) {
            $index = $this->index;
        }

        $resources = json_decode(file_get_contents($this->persistence), true)[$index];

        if ($createModelInstance) {
            foreach ($resources as $resource) {
                $models[] = $this->newModelInstance($resource);
            }
            return $models;
        }

        return $resources;
    }

    /**
     * @param array $models
     * @param array $params
     * @return array
     */
    protected function checkParams(array $models, array $params)
    {
        foreach ($params as $index => $param) {
            if ($param instanceof Model) {
                $models = $this->checkByRelation($index, $param, $models);
            } else {
                $models = $this->checkByAttribute($index, $param, $models);
            }
        }

        return $models;
    }

    /**
     * @param $index
     * @param $param
     * @param array $allModels
     * @return array
     */
    protected function checkByAttribute($index, $param, array $allModels)
    {
        $models = [];
        foreach ($allModels as $modelIndex => $model) {
            if ($model->{$index} == $param) {
                $models[$modelIndex] = $model;
            }
        }

        return $models;
    }

    /**
     * @param $index
     * @param Model $givenModel
     * @param array $models
     * @return array
     * @throws \Exception
     */
    protected function checkByRelation($index, Model $givenModel, array $models)
    {
        $validator = 0;
        foreach ($this->relations as $relation => $modelRelated) {
            if ($modelRelated[$index]['class'] === get_class($givenModel)) {
                $indexRelation = $modelRelated[$index]['indexRelation'];
                $relationModels = $this->getManyToManyRelationsWith($indexRelation, $index, $givenModel->id);
                $models = $this->diff($models, $relationModels, $index, $relation);
                $validator++;
            }
        }

        if ($validator === 0) {
            throw new \Exception('There is no relation between '. get_class($this). ' and' .get_class($givenModel));
        }

        return $models;
    }

    /**
     * @param array $models
     * @param array $relationModels
     * @param $relationName
     * @param $relationType
     * @return array
     */
    protected function diff(array $models, array $relationModels, $relationName, $relationType)
    {
        foreach ($models as $indexModel => $oneModel) {
            $isOnRelation = false;
            $mappedBy = $oneModel->getRelations()[$relationType][$relationName]['mappedBy'];
            foreach ($relationModels as $relationModel) {
                if ($oneModel->id === $relationModel[$mappedBy]) {
                    $isOnRelation = true;
                }
            }

            if (!$isOnRelation) {
                unset($models[$indexModel]);
            }
        }

        return $models;
    }

    /**
     * @return mixed
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param $indexRelation
     * @param $index
     * @param $relatedModelId
     * @return array
     */
    protected function getManyToManyRelationsWith($indexRelation, $index, $relatedModelId)
    {
        $models = [];
        $allModels = $this->getAllModels($indexRelation, false);

        foreach ($allModels as $model) {
            if ($model[$index] === $relatedModelId) {
                $models[] = $model;
            }
        }

        return $models;
    }
}