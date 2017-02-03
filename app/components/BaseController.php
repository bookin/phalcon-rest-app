<?php
namespace Rest\Components;


class BaseController extends \Phalcon\DI\Injectable{
    public function __construct(){
        $di = \Phalcon\DI::getDefault();
        $this->setDI($di);
    }
}