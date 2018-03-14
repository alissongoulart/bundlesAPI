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
        $broadBandsTree = [];
        $broadBands = $this->getBroadBands();

        foreach ($broadBands as $broadBand) {
            $broadBandsTree = array_merge($broadBandsTree, $this->getTree([$broadBand]));
        }

        $combinations = $this->setCombinations($broadBandsTree, "", 0);

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
    private function setCombinations($bundles, $name, $price)
    {
        $combinations = [];
        foreach ($bundles as $bundle) {
            $name = $this->setCombinationName($name, $bundle["name"]);
            $price += $bundle["price"];
            $combinations[] = $this->addCombination($name, $price);
            if (count($bundle["children"]) > 0) {
                $combinations = array_merge(
                    $combinations,
                    $this->setCombinations($bundle["children"], $name, $price)
                );
            }

            $name = $this->clearCombinationName($name, $bundle["name"]);
            $price -= $bundle["price"];
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

    private function getNormalizedTree($bundles)
    {
        $normalizedThree = [];
        foreach ($bundles as $indexBundle => $bundle) {
            $bundles[$indexBundle]["children"] = array_merge($normalizedThree, $bundles[$indexBundle]["children"]);
            if (count($bundle["children"]) > 0) {
                $normalizedThree1 = $normalizedThree;
                for ($j = count($normalizedThree1); $j < count($bundles[$indexBundle]["children"]); $j++) {
                    $aux = $bundles[$indexBundle]["children"][$j]["children"];
                    $bundles[$indexBundle]["children"][$j]["children"] = array_merge($normalizedThree1, $bundles[$indexBundle]["children"][$j]["children"]);
                    if (count($aux) > 0) {
                        $normalizedThree2 = $normalizedThree1;
                        for ($k = count($normalizedThree2); $k < count($bundles[$indexBundle]["children"][$j]["children"]); $k++) {
                            $aux = $bundles[$indexBundle]["children"][$j]["children"][$k]["children"];
                            $bundles[$indexBundle]["children"][$j]["children"][$k]["children"] = array_merge($normalizedThree2, $bundles[$indexBundle]["children"][$j]["children"][$k]["children"]);
                            if (count($aux) > 0) {

                            }
                            $normalizedThree2[] = $bundles[$indexBundle]["children"][$j]['children'][$k];
                        }
                    }
                    $normalizedThree1[] = $bundles[$indexBundle]["children"][$j];
                }
            }
            $normalizedThree[] = $bundles[$indexBundle];
        }

        return $bundles;
    }
    
    
    public function getTree($bundles)
    {
        $tree = [];
        foreach ($bundles as $index => $bundle) {
            $children = [];
            if (count($bundle->getRelatedBundles()) > 0) {
                foreach ($bundle->getRelatedBundles() as $relatedBundle) {
                    $children1 = [];
                    if (count($relatedBundle->getRelatedBundles()) > 0) {
                        foreach ($relatedBundle->getRelatedBundles() as $relatedBundleChild) {
                            $children1[] = [
                                "name" => $relatedBundleChild->name,
                                "type" => $relatedBundleChild->type,
                                "price" => $relatedBundleChild->price,
                                "children" => []
                            ];
                        }
                    }

                    $children[] = [
                        "name" => $relatedBundle->name,
                        "type" => $relatedBundle->type,
                        "price" => $relatedBundle->price,
                        "children" => $children1
                    ];
                }
            }

            $tree[] = [
                "name" => $bundle->name,
                "type" => $bundle->type,
                "price" => $bundle->price,
                "children" => $children
            ];

        }
        $tree = $this->getNormalizedTree($tree);

        return $tree;
    }
}