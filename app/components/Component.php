<?php
namespace Rest\components;

use Phalcon\Di;

class Component  extends \Phalcon\DI\Injectable
{
    public function __construct(){
        $di = Di::getDefault();
        $this->setDI($di);
    }
}