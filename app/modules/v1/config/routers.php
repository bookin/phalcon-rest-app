<?php

$collections = [];

/**
 * Collection for UploadController
 */
$collection = new \Phalcon\Mvc\Micro\Collection();
$collection->setPrefix('/v1/upload')->setHandler(v1\Controllers\UploadController::class, true);

$collection->get('/get-url', 'getUrl'); //get url for video hosting
$collection->post('/video/{rand}', 'uploadVideo'); //fake method for upload video

$collections[] = $collection;


/**
 * Collection for VideoController
 */
$collection = new \Phalcon\Mvc\Micro\Collection();
$collection->setPrefix('/v1/video')->setHandler(v1\Controllers\VideoController::class, true);

$collection->get('/', 'index'); //get video list
$collection->get('/{id}', 'view'); //get information about video
$collection->head('/trim/{id}', 'checkTrim'); //check trim processing
$collection->post('/trim/{id}', 'startTrim'); //request for trim video by ID
$collection->put('/trim/{id}', 'restartTrim'); //request for trim video by ID


$collections[] = $collection;


/**
 * Collection for UserController
 */
$collection = new \Phalcon\Mvc\Micro\Collection();
$collection->setPrefix('/v1/auth')->setHandler(v1\Controllers\UserController::class, true);

$collection->get('/', 'auth'); //just return fake token

$collections[] = $collection;



return $collections;