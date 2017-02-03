<?php
namespace v1\Controllers;

use Rest\Components\RestController;

class VideoController extends RestController{

    public function index()
    {
        return $this->response(['video']);
    }
}