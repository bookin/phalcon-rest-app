<?php
namespace v1\Controllers;

use Rest\Components\RestController;
use v1\components\CDNServer;

class UploadController extends RestController {

    /**
     * Action for return server_url to upload video
     * @return mixed
     */
    public function getUrl()
    {
        $cdn = new CDNServer();
        return $this->response([
            'server_url'=>$this->di->get('url')->get('v1/upload/video/'.$cdn->getServerKey())
        ]);
    }

    public function uploadVideo()
    {
        return $this->response(['upload']);
    }
}