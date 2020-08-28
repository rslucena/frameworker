<?php
declare(strict_types=1);

use src\Config\Config;
use src\Router\Router;
use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

//INIT

$http_router = new Router();
$http_config = new Config();
$http_worker = new Worker('http://0.0.0.0:80');

//WORKERS
$http_worker->count = 4;

//RECEIVED MESSAGE
$http_worker->onMessage = function ($connection, $request) {

    //$request->get();
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