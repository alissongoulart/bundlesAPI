<?php
$routes = new \Phroute\Phroute\RouteCollector();
$routes->get('list-all-broadband', ['BundlesController','getBroadBandCombinations']);