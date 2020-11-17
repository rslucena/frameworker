<?php
    declare(strict_types=1);

    use src\Config\Config;
    use src\Router\Router;
    use src\Database\Database;

    require_once __DIR__.'/vendor/autoload.php';

    //INIT
    $Config = new Config();
    $Router = new Router();

    //$request->get();
    //$request->post();
    //$request->header();
    //$request->cookie();
    //$requset->session();
    //$request->path();

    try {

        // HOME
        $Router->map('GET', '/', 'HomeController#HomePage', 'home');

        // USER
        $Router->addRoutes(array(
            array('GET', '/user?/?', 'UserController#UserPage', 'UserPage'),
            array('GET', '/user/[i:id]?/?', 'UserController#UpdatePage', 'UserDetails'),
            array('POST', '/user?/?', 'UserController#UpdatePage', 'UserUpdate')
        ));

        $match = $Router->run();

        if (is_array($match)) {

            $ControllerTarget = "src\Controller\\{$match['class']}";

            if (!class_exists($ControllerTarget)) {
                header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
                die();
            }

            $Controller = new $ControllerTarget;

            call_user_func_array(array($Controller, $match['func']), array(&$match));

            die();

        } else {

            header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
            die();

        }


    } catch (Exception $exception) {

        var_dump($exception->getMessage());

    }


    //    $Router->on($Router->method(), $Router->uri(), function ( $module, $class, $method ){
    //        var_dump($module);
    //        var_dump($class);
    //        var_dump($method);
    //    });

    //    echo $Router->run($Router->method(), $Router->uri());
