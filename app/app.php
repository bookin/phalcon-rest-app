<?php

use Phalcon\DI\FactoryDefault as DI;

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Rest' => APP_PATH.DIRECTORY_SEPARATOR
])->register();


/**
 * The DI is our direct injector.  It will store pointers to all of our services
 * and we will insert it into all of our controllers.
 * @var DI
 */
$di = new DI();

/**
 * Return array of the Collections, which define a group of routes, from
 * routes/collections.  These will be mounted into the app itself later.
 */
/*$di->set('collections', function(){
    $routers = include(CONFIG_PATH.'/routers.php');
    return $routers;
});*/

/**
 * $di's setShared method provides a singleton instance.
 * If the second parameter is a function, then the service is lazy-loaded
 * on its first instantiation.
 */
$di->setShared('config', function() {
    return new \Phalcon\Config(require CONFIG_PATH.'/config.php');
});

// As soon as we request the session service, it will be started.
$di->setShared('session', function(){
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

$di->set('modelsCache', function() {

    //Cache data for one day by default
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        'lifetime' => 3600
    ));

    //File cache settings
    $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
        'cacheDir' => __DIR__ . '/cache/'
    ));

    return $cache;
});

/**
 * Init MongoDb
 */
$di->set("mongo", function () {
    $config = $this->get('config')->mongodb;
    $mongo = new MongoClient(
        $config->server?:'mongodb://localhost:27017',
        isset($config->options)?$config->options->toArray():['connect'=>true],
        isset($config->driver_options)?$config->driver_options->toArray():[]
    );
    return $mongo->selectDB($config->db);
}, true);

$di->set("collectionManager", function () {
    return new Phalcon\Mvc\Collection\Manager();
}, true);

/**
 * If our request contains a body, it has to be valid JSON.  This parses the
 * body into a standard Object and makes that vailable from the DI.  If this service
 * is called from a function, and the request body is nto valid JSON or is empty,
 * the program will throw an Exception.
 */
$di->setShared('requestBody', function() {
    $in = file_get_contents('php://input');
    $in = json_decode($in, FALSE);

    // JSON body could not be parsed, throw exception
    if($in === null){
        throw new HTTPException(
            'There was a problem understanding the data sent to the server by the application.',
            409,
            array(
                'dev' => 'The JSON body sent to the server was unable to be parsed.',
                'internalCode' => 'REQ1000',
                'more' => ''
            )
        );
    }

    return $in;
});

$di->set('url', function(){
    $url = new Phalcon\Mvc\Url();
    $url->setBaseUri($this->get('config')->app->domain);
    return $url;
});


$loader = new \Rest\Components\Loader($di);
$loader->loadModules();

/**
 * Out application is a Micro application, so we mush explicitly define all the routes.
 * For APIs, this is ideal.  This is as opposed to the more robust MVC Application
 * @var $app
 */
$app = new Phalcon\Mvc\Micro();
$app->setDI($di);

/**
 * Before every request, make sure user is authenticated.
 * Returning true in this function resumes normal routing.
 * Returning false stops any route from executing.
 */

/*
This will require changes to fit your application structure.
It supports Basic Auth, Session auth, and Exempted routes.

It also allows all Options requests, as those tend to not come with
cookies or basic auth credentials and Preflight is not implemented the
same in every browser.
*/

/*
$app->before(function() use ($app, $di) {

    // Browser requests, user was stored in session on login, replace into DI
    if ($di->getShared('session')->get('user') != false) {
        $di->setShared('user', function() use ($di){
            return $di->getShared('session')->get('user');
        });
        return true;
    }

    // Basic auth, for programmatic responses
    if($app->request->getServer('PHP_AUTH_USER')){
        $user = new \PhalconRest\Controllers\UsersController();
        $user->login(
            $app->request->getServer('PHP_AUTH_USER'),
            $app->request->getServer('PHP_AUTH_PW')
        );
        return true;
    }


    // All options requests get a 200, then die
    if($app->__get('request')->getMethod() == 'OPTIONS'){
        $app->response->setStatusCode(200, 'OK')->sendHeaders();
        exit;
    }


    // Exempted routes, such as login, or public info.  Let the route handler
    // pick it up.
    switch($app->getRouter()->getRewriteUri()){
        case '/users/login':
            return true;
            break;
        case '/example/route':
            return true;
            break;
    }

    // If we made it this far, we have no valid auth method, throw a 401.
    throw new \PhalconRest\Exceptions\HTTPException(
        'Must login or provide credentials.',
        401,
        array(
            'dev' => 'Please provide credentials by either passing in a session token via cookie, or providing password and username via BASIC authentication.',
            'internalCode' => 'Unauth:1'
        )
    );

    return false;
});*/


/**
 * Mount all of the collections, which makes the routes active.
 */
if($di->has('collections')){
    foreach($di->get('collections') as $collection){
        if(is_object($collection) && $collection instanceof \Phalcon\Mvc\Micro\Collection)
            $app->mount($collection);
    }
}

/**
 * The base route return the list of defined routes for the application.
 * This is not strictly REST compliant, but it helps to base API documentation off of.
 * By calling this, you can quickly see a list of all routes and their methods.
 */
$app->get('/', function() use ($app){
    $routes = $app->getRouter()->getRoutes();
    $routeDefinitions = array('GET'=>array(), 'POST'=>array(), 'PUT'=>array(), 'PATCH'=>array(), 'DELETE'=>array(), 'HEAD'=>array(), 'OPTIONS'=>array());
    foreach($routes as $route){
        $method = $route->getHttpMethods();
        $routeDefinitions[$method][] = $route->getPattern();
    }
    $response = new \Rest\Components\Response\JsonResponse();
    $response->send($routeDefinitions);
    return $routeDefinitions;
});


/**
 * The notFound service is the default handler function that runs when no route was matched.
 * We set a 404 here unless there's a suppress error codes.
 */
$app->notFound(function () use ($app) {
    throw new Rest\Components\RestException(
        404,
        [
            'devMessage' => 'That route was not found on the server.',
            'devCode' => 'NF1000',
            'devInfo' => 'Check route for misspellings.'
        ]
    );
});


set_exception_handler(function($exception) use ($app){
    if(is_a($exception, 'Rest\\Components\\RestException')){
        /** @var $exception Rest\Components\RestException */
        $exception->send();
    }
    error_log($exception);
    error_log($exception->getTraceAsString());
});

return $app;
