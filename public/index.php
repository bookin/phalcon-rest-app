<?php
defined('APP_ENV') or define('APP_ENV', 'dev');


define('APP_PATH', realpath('..'.DIRECTORY_SEPARATOR.'app'));
define('MODULES_PATH', APP_PATH.DIRECTORY_SEPARATOR.'modules');
define('CONFIG_PATH', APP_PATH.DIRECTORY_SEPARATOR.'config');
define('PUBLIC_PATH', __DIR__);

try{
    /** @var \Phalcon\Mvc\Micro $app */
    $app = require_once APP_PATH.DIRECTORY_SEPARATOR.'app.php';

    $app->handle();
}catch (\Exception $e){
    $exception = new \Rest\Components\RestException($e->getCode()?:500, '', [
        'devMessage'=>$e->getMessage()
    ]);
    $exception->send();
}