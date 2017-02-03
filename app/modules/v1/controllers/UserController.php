<?php
namespace v1\Controllers;

use Rest\Components\RestController;

class UserController extends RestController{

    public function index()
    {
        return $this->response(['user']);
    }
}