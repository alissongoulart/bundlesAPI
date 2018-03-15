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
        $tree = [];
        $broadBands = $this->getBroadBands();

        foreach ($broadBands as $broadBand) {
            //Get the default tree, according the challenge proposed
            $tree = array_merge($tree, $this->getTree([$broadBand]));
        }

        //Normalize the tree in order to go through all possible combinations
        $this->normalizeTree($tree, []);

        //Get all combinations
        $combinations = $this->setCombinations($tree, "", 0, []);

        return json_encode($this->sortByPrice($combinations));
    }

    /**
     * @param $array
     * @return array
     */
    private function sortByPrice($array)
    {
        usort($array, function($a, $b) {
            return $a['price'] - $b['price'];
        });

        return $array;
    }

    /**
     * @return \App\Support\Model\Model|array
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
    private function setCombinations($bundles, $name, $price, $addedValues)
    {
        $combinations = [];
        foreach ($bundles as $bundle) {
            if (!$this->existRestriction($addedValues, $bundle)) {
                $name = $this->setCombinationName($name, $bundle["name"]);
                $price += $bundle["price"];
                $combinations[] = $this->addCombination($name, $price);
                $addedValues[$bundle["name"]] = [
                    "type" => $bundle['type'],
                    "name" => $bundle['name']
                ];

                if (count($bundle["children"]) > 0) {
                    $combinations = array_merge(
                        $combinations,
                        $this->setCombinations($bundle["children"], $name, $price, $addedValues)
                    );
                }

                unset($addedValues[$bundle["name"]]);
                $name = $this->clearCombinationName($name, $bundle["name"]);
                $price -= $bundle["price"];
            }

        }

        return $combinations;
    }

    /**
     * Check if there is some restriction in add the bundle at the chain.
     * @param $addedValues
     * @param $bundle
     * @return bool
     */
    private function existRestriction($addedValues, $bundle)
    {
        if (count($addedValues) > 0) {
            foreach ($addedValues as $addedValue) {
                if ($bundle["type"] == $addedValue["type"]) {
                    if ($bundle["type"] !== "addon") {
                       return true;
                    }

                    if ($bundle["name"] == $addedValues['name']) {
                        return true;
                    }
                }
            }
        }

        return false;
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

    /**
     * @param $nodes
     * @param $parent
     */
    private function normalizeTree(&$nodes, $parent)
    {
        $tempNodes = $parent;

        for ($i = count($tempNodes); $i < count($nodes); $i++) {
            $countChildren = count($nodes[$i]["children"]);
            $nodes[$i]["children"] = array_merge($tempNodes, $nodes[$i]["children"]);
            if ($countChildren > 0) {
                $this->normalizeTree($nodes[$i]["children"], $tempNodes);
            }
            $tempNodes[] = $nodes[$i];
        }
    }

    /**
     * @param $bundles
     * @return array|mixed
     */
    private function getTree($bundles)
    {
        $tree = [];
        foreach ($bundles as $bundle) {
            $tree[] = [
                "name" => $bundle->name,
                "type" => $bundle->type,
                "price" => $bundle->price,
                "children" => $this->getTree($bundle->getRelatedBundles())
            ];
        }
        return $tree;
    }
}