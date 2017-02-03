<?php
defined('APP_ENV') or define('APP_ENV', 'dev');


define('APP_PATH', realpath('..'.DIRECTORY_SEPARATOR.'app'));
define('MODULES_PATH', APP_PATH.DIRECTORY_SEPARATOR.'modules');
define('CONFIG_PATH', APP_PATH.DIRECTORY_SEPARATOR.'config');

/** @var \Phalcon\Mvc\Micro $app */
$app = require_once APP_PATH.DIRECTORY_SEPARATOR.'app.php';

$app->handle();