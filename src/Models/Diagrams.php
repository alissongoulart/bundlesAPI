<?php

namespace App\Models;

use App\Support\Model\Model;

class Diagrams extends Model
{
    protected $index = "diagram";
    protected $relations = [
        'hasMany' => [
            'class' => Bundles::class,
            'indexRelation' => 'diagramBundle',
            'mappedBy' => 'diagram'
        ]
    ];
}