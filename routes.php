<?php
$routes = new \Phroute\Phroute\RouteCollector();
$routes->post('list-all-broadband', ['BundlesController','getBroadBandCombinations']);