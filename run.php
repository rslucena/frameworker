<?php

use src\Router\Router;
use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

//INIT
$http_router = new Router();
$http_worker = new Worker('http://0.0.0.0:80');

//WORKERS
$http_worker->count = 4;

//RECEIVED MESSAGE
$http_worker->onMessage = function ($connection, $request) {

    //REQUEST
    $request->get();
    $request->post();
    $request->header();
    $request->cookie();
    $request->session();
    $request->uri();
    $request->path();
    $request->method();

    //RESPONSE
    $connection->send('teste');
};

Worker::runAll();