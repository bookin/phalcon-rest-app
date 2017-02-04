<?php
/**
 * @var \Phalcon\Di\FactoryDefault $di
 */

/**
 * Set config
 */
$di->setShared('config', function() {
    return new \Phalcon\Config(require CONFIG_PATH.'/config.php');
});

/**
 * Set session component
 */
$di->setShared('session', function(){
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

/**
 * Set Cache options
 */
$di->set('modelsCache', function() {
    $config = $this->getConfig()->app;
    if(isset($config->cache) && $config->cache->enable){
        //Cache data for one day by default
        $frontCache = new \Phalcon\Cache\Frontend\Data(array(
            'lifetime' => $config->cache->lifetime?:3600
        ));

        //File cache settings
        $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
            'cacheDir' => $config->cache->cacheDir?:__DIR__ . '/cache/'
        ));
    }

    return $cache;
});

/**
 * Set config MongoDb
 */
$di->set("mongo", function () {
    $config = $this->getConfig()->mongodb;
    $mongo = new MongoClient(
        $config->server?:'mongodb://localhost:27017',
        isset($config->options)?$config->options->toArray():['connect'=>true],
        isset($config->driver_options)?$config->driver_options->toArray():[]
    );
    return $mongo->selectDB($config->db);
}, true);

/**
 * Set ODM
 */
$di->set("collectionManager", function () {
    return new Phalcon\Mvc\Collection\Manager();
}, true);


/**
 * Set Url component
 */
$di->set('url', function(){
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri($this->get('config')->app->domain);
    return $url;
});

/**
 * If our request contains a body, it has to be valid JSON.
 */
$di->setShared('requestBody', function() {
    $in = file_get_contents('php://input');
    $in = json_decode($in, FALSE);

    /* JSON body could not be parsed, throw exception */
    if($in === null){
        throw new \Rest\Components\RestException(
            409,
            'There was a problem understanding the data sent to the server by the application.',
            [
                'devMessage' => 'The JSON body sent to the server was unable to be parsed.',
                'devCode' => 'REQ1000',
                'devInfo' => ''
            ]
        );
    }

    return $in;
});