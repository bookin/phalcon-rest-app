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

$collection->get('/requests', 'getRequests'); //get requests list
$collection->get('/requests/{request_id}', 'viewRequest'); //get processing status by request_id

$collection->post('/trim/{video_id}', 'startTrim'); //request for trim video by video_id, parameters: from, to
$collection->put('/trim/{request_id}', 'restartTrim'); //restart request for trim video by request_id


$collections[] = $collection;


/**
 * Collection for UserController
 */
$collection = new \Phalcon\Mvc\Micro\Collection();
$collection->setPrefix('/v1/auth')->setHandler(v1\Controllers\AuthController::class, true);

$collection->get('/', 'getToken'); //just return fake token

$collections[] = $collection;



return $collections;