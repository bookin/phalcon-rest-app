<?php

$rout = new \Rest\Components\Route\Routes();

$rout->add(
    v1\Controllers\UploadController::class,
    '/v1/upload',
    [
        $rout->get('/get-url', 'getUrl'), //get url for video hosting
        $rout->post('/video/{rand}', 'uploadVideo') //fake method for upload video
    ],
    true
);

$rout->add(
    v1\Controllers\VideoController::class,
    '/v1/video',
    [
        $rout->get('/', 'index'), //get video list
        $rout->get('/{id}', 'view'), //get information about video
        $rout->get('/requests', 'getRequests'), //get requests list
        $rout->get('/requests/{request_id}', 'viewRequest'), //get processing status by request_id
        $rout->put('/requests/{request_id}', 'restartTrim'), //restart request for trim video by request_id
        $rout->post('/trim/{video_id}', 'startTrim'), //request for trim video by video_id, parameters: from, to
    ],
    true
);

$rout->add(
    v1\Controllers\AuthController::class,
    '/v1/auth',
    [
        $rout->get('/', 'getToken'), //just return fake token
    ],
    true
);

return $rout->getCollections();