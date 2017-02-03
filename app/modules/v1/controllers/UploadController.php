<?php
namespace v1\Controllers;

use Rest\Components\RestController;

class UploadController extends RestController {

    public function index()
    {
        return $this->response(['upload']);
    }
}