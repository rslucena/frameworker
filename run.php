<?php
declare(strict_types=1);

use src\Config\Config;
use src\Router\Router;
use Workerman\MySQL\Connection;
use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

//INIT
$http_router = new Router();
$http_config = new Config();
$http_worker = new Worker('http://127.0.0.1:80');

//WORKERS
$http_worker->name = "httpServer";
$http_worker->count = 4;

//START WORKERMAN
$http_worker->onWorkerStart = function() use ($http_config) {

    echo "onWorkerStart" . PHP_EOL;

};

//OPEN CONNECT
$http_worker->onConnect = function ($connection) {

    echo "onConnect" . PHP_EOL;

};

//RECEIVED MESSAGE
$http_worker->onMessage = function ($connection, $request) use ($http_router) {

    var_dump($http_router);



    echo "onMessage" . PHP_EOL;

//    var_dump($http_router);

//    $router->get('/some/route', function($request) {
//        // The $request argument of the callback
//        // will contain information about the request
//        return "Content";
//    });


//    print_r($request);

//    $request->get();
    //$request->post();
    //$request->header();
    //$request->cookie();
    //$requset->session();
    //$request->uri();
    //$request->path();
    //$request->method();

    // Send data to client
    $connection->send("Hello World");
};


Worker::runAll();