<?php

namespace App\Support;
use App\Controllers\BundlesController;
use App\Models\Bundles;
use App\Models\Diagrams;
use Pimple\Container;

class AppContainer
{
    private $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->container['BundlesController'] = function($c){
            return new BundlesController(new Bundles(), new Diagrams());
        };
    }

    /**
     * @return Container
     */
    public function getInstance()
    {
        return $this->container;
    }
}