<?php

namespace App\Models;

use App\Support\Model\Model;

class Diagram extends Model
{
    protected $index = "diagram";
    protected $relations = [
        'hasMany' => [
            'class' => Bundle::class,
            'indexRelation' => 'diagramBundle',
            'mappedBy' => 'diagram'
        ]
    ];
}