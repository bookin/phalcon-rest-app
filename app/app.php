<?php

use Phalcon\DI\FactoryDefault as DI;

/**
 * The DI is our direct injector.  It will store pointers to all of our services
 * and we will insert it into all of our controllers.
 * @var DI
 */
$di = new DI();

/**
 * Include Services
 */
include CONFIG_PATH.DIRECTORY_SEPARATOR.'services.php';

/**
 * Include Autoloader
 */
include CONFIG_PATH.DIRECTORY_SEPARATOR.'loader.php';

/**
 * Out application is a Micro application
 * @var $app
 */
$app = new Phalcon\Mvc\Micro();
$app->setDI($di);

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
 * Before all requests check Bearer token and auth, or not
 */
$app->before(function() use($app, $di){
    $allow_requests = ['/v1/auth'];
    if(in_array($app->request->getURI(), $allow_requests)){
        return true;
    }

    $authHeader = $app->request->getHeader('Authorization');
    if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
        $token = $matches[1];
        if(($model=\Rest\models\Tokens::findFirst([['token'=>$token]]))){
            $di->setShared('user', function()use($model){
                return $model;
            });
        }else{
            throw new \Rest\Components\RestException(
                403,
                'Invalid token error',
                [
                    'devCode' => 'BEFORE1'
                ]
            );
        }
        //check $matches[1] (token)
        //get user, and set like service
        //if valid
        /* TODO: move auth methods to classes, and add opportunity override in the modules*/
        return true;
    }

    throw new \Rest\Components\RestException(
        401,
        'Need authorized',
        [
            'devCode' => 'BEFORE1'
        ]
    );
});

/**
 * Show error or list of defined routes
 */
$app->get('/', function() use ($app){
    if(APP_ENV == 'dev'){
        /* Show list of defined routes */
        $routes = $app->getRouter()->getRoutes();
        $routeDefinitions = array('GET'=>array(), 'POST'=>array(), 'PUT'=>array(), 'PATCH'=>array(), 'DELETE'=>array(), 'HEAD'=>array(), 'OPTIONS'=>array());
        foreach($routes as $route){
            $method = $route->getHttpMethods();
            $routeDefinitions[$method][] = $route->getPattern();
        }
        $response = new \Rest\Components\Response\JsonResponse();
        $response->send($routeDefinitions);
        return $routeDefinitions;
    }else{
        throw new Rest\Components\RestException(403);
    }
});


/**
 * The notFound service is the default handler function that runs when no route was matched.
 * We set a 404 here unless there's a suppress error codes.
 */
$app->notFound(function () use ($app) {
    throw new Rest\Components\RestException(
        404,
        'That route was not found on the server.',
        [
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
