<?php

$collections = [];

$collection = new \Phalcon\Mvc\Micro\Collection();

$collection->setPrefix('/v1/upload')->setHandler(v1\Controllers\UploadController::class, true);
$collection->get('/', 'index');

$collections[] = $collection;

$collection = new \Phalcon\Mvc\Micro\Collection();

$collection->setPrefix('/v1/video')->setHandler(v1\Controllers\VideoController::class, true);
$collection->get('/', 'index');

$collections[] = $collection;


return $collections;