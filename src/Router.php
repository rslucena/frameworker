<?php

declare(strict_types=1);

namespace src\Router;

/**
 * Class Router
 * @package src\Router
 */
class Router
{
    private $routes = [];

    /**
     *
     * @param $method
     * @param $path
     * @param $callback
     * @return $this
     */
    public function on($method, $path, $callback)
    {
        $method = strtolower($method);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $uri = substr($path, 0, 1) !== '/' ? '/' . $path : $path;
        $pattern = str_replace('/', '\/', $uri);
        $route = '/^' . $pattern . '$/';

        $this->routes[$method][$route] = $callback;

        return $this;
    }

    /**
     * @param $method
     * @param $uri
     * @return mixed|null
     */
    function run($method, $uri)
    {
        $method = strtolower($method);
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $callback) {

            if (preg_match($route, $uri, $parameters)) {
                array_shift($parameters);
                return call_user_func_array($callback, $parameters);
            }
        }
        return null;
    }
}
