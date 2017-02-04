<?php

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Rest' => APP_PATH.DIRECTORY_SEPARATOR
])->register();

/**
 * Autoload modules
 */
$loader = new \Rest\Components\Loader($di);
$loader->loadModules();