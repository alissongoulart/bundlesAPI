<?php

namespace App\Controllers;

use App\Support\Model\Contracts\ModelInterface;

class BundlesController
{
    private $bundle;
    private $diagram;

    /**
     * Controller constructor.
     * @param ModelInterface $bundle
     * @param ModelInterface $diagram
     */
    public function __construct(ModelInterface $bundle, ModelInterface $diagram)
    {
        $this->bundle = $bundle;
        $this->diagram = $diagram;
    }

    /**
     * @return string
     */
    public function getBroadBandCombinations()
    {
        $broadBands = $this->getBroadBands();
        $combinations = $this->setCombinations($broadBands, "", 0);

        return json_encode($this->sortByPrice($combinations));
    }

    /**
     * @param $array
     * @return array
     */
    private function sortByPrice($array)
    {
        $array[] = usort($array, function($a, $b) {
            return $a['price'] - $b['price'];
        });

        return $array;
    }

    /**
     * @return \App\Support\Model\Contracts\Model|array
     */
    private function getBroadBands()
    {
        $diagram = $this->diagram->findOneByAttributes([
            'name' => 'Diagram1'
        ]);

        $broadBands = $this->bundle->findAllByAttributes([
            'type' => 'bb',
            'diagram' => $diagram
        ]);

        return $broadBands;
    }

    /**
     * @param $name
     * @param $price
     * @return array
     */
    private function addCombination($name, $price)
    {

        return [
            'name' => $name,
            'price' => $price
        ];
    }

    /**
     * @param $bundles
     * @param $name
     * @param $price
     * @return array
     */
    private function setCombinations($bundles, $name, $price)
    {
        $combinations = [];
        foreach ($bundles as $bundle) {
            $name = $this->setCombinationName($name, $bundle->name);
            $price += $bundle->price;
            $combinations[] = $this->addCombination($name, $price);
            if (count($bundle->getRelatedBundles()) > 0) {
                $combinations = array_merge(
                    $combinations,
                    $this->setCombinations($bundle->getRelatedBundles(), $name, $price)
                );
            }

            $name = $this->clearCombinationName($name, $bundle->name);
            $price -= $bundle->price;
        }

        return $combinations;
    }

    /**
     * @param $name
     * @param $bundleName
     * @return string
     */
    private function setCombinationName($name, $bundleName)
    {
        if ($name != "") {
            $name .= " + " . $bundleName;
        } else {
            $name = $bundleName;
        }

        return $name;
    }

    /**
     * @param $name
     * @param $bundleName
     * @return mixed|string
     */
    private function clearCombinationName($name, $bundleName)
    {
        if (strstr($name, " + " . $bundleName) !== false) {
            return str_replace(" + " . $bundleName, "", $name);
        } else {
            return "";
        }
    }
}