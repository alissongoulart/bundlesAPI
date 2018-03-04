<?php

namespace App\Models;

use App\Support\Model\Model;

class Bundles extends Model
{
    protected $index = 'bundle';
    protected $relations = [
        'belongsToMany' => [
            'diagram' => [
                'class' => Diagrams::class,
                'indexRelation' => 'diagramBundle',
                'mappedBy' => 'bundle'
            ],
            'from' => [
                'class' => Bundles::class,
                'indexRelation' => 'bundleRelation',
                'mappedBy' => 'to'
            ]
        ]
    ];

    /**
     * Get Bundle objects directly bind to the instance
     * @return array
     */
    public function getRelatedBundles()
    {
        $bundles = $this->findAllByAttributes([
            'from' => $this
        ]);

        foreach ($bundles as $indexBundle => $bundle) {
            $bundles[$indexBundle] = $this->sumOperationValue($bundle);
        }

        return $bundles;
    }

    /**
     * @param $relatedBundle
     * @return mixed
     */
    protected function sumOperationValue($relatedBundle)
    {
        $allModels = $this->getAllModels('bundleRelation', false);

        foreach ($allModels as $model) {
            if ($model['from'] === $this->id && $model['to'] == $relatedBundle->id) {
                $relatedBundle->price += $model['operationValue'];
            }
        }

        return $relatedBundle;
    }


}