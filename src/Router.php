<?php

    declare(strict_types=1);

    namespace src\Router;

    use Exception;

    /**
     * Class Router
     * @package src\Router
     * @link https://github.com/dannyvankooten/AltoRouter
     */
    class Router
    {

        /**
         * @var array Array of all routes (incl. named routes).
         */
        protected $routes = array();

        /**
         * @var array Array of all named routes.
         */
        protected $namedRoutes = array();

        /**
         * @var string Can be used to ignore leading part of the
         * Request URL (if main file lives in subdirectory of host)
         */
        protected $basePath = '';

        /**
         * @var array Array of default match types (regex helpers)
         */
        protected $matchTypes = [
            'i' => '[0-9]++', 'a' => '[0-9A-Za-z]++', 'h' => '[0-9A-Fa-f]++', '*' => '.+?', '**' => '.++',
            '' => '[^/\.]++'
        ];

        /**
         ** Create router in one call from config.
         *
         * @param  array  $routes
         * @param  string  $basePath
         * @param  array  $matchTypes
         * @throws \Exception
         */
        public function __construct(array $routes = array(), $basePath = '', array $matchTypes = array())
        {

            $this->addRoutes($routes);
            $this->setBasePath($basePath);
            $this->addMatchTypes($matchTypes);
        }

        /**
         * Add multiple routes at once from array in the following format:
         *
         *   $routes = [
         *      [$method, $route, $target, $name]
         *   ];
         *
         * @param  array  $routes
         * @return void
         * @throws Exception
         * @author Koen Punt
         */
        public function addRoutes($routes)
        {

            if (!is_array($routes)) {
                throw new Exception('Routes should be an array.');

            }

            foreach ($routes as $route) {
                call_user_func_array([$this, 'map'], $route);
            }

        }

        /**
         * Set the base path.
         * Useful if you are running your application from a subdirectory.
         * @param  string  $basePath
         */
        public function setBasePath($basePath)
        {

            $this->basePath = $basePath;
        }

        /**
         * Add named match types. It uses array_merge so keys can be overwritten.
         *
         * @param  array  $matchTypes  The key is the name and the value is the regex.
         */
        public function addMatchTypes(array $matchTypes)
        {
            $this->matchTypes = array_merge($this->matchTypes, $matchTypes);
        }

        /**
         * Retrieves all routes.
         * Useful if you want to process or display routes.
         * @return array All routes.
         */
        public function getRoutes()
        {
            return $this->routes;
        }

        /**
         * Map a route to a target
         *
         * @param  string  $method  One of 5 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PATCH|PUT|DELETE)
         * @param  string  $route  The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
         * @param  mixed  $target  The target where this route should point to. Can be anything.
         * @param  string  $name  Optional name of this route. Supply if you want to reverse route this url in your application.
         * @throws Exception
         */
        public function map($method, $route, $target, $name = null)
        {

            $this->routes[] = [$method, $route, $target, $name];

            if ($name) {
                if (isset($this->namedRoutes[$name])) {
                    throw new Exception("Can not redeclare route '{$name}'");
                }
                $this->namedRoutes[$name] = $route;
            }

            return;
        }

        /**
         * Reversed routing
         *
         * Generate the URL for a named route. Replace regexes with supplied parameters
         *
         * @param  string  $routeName  The name of the route.
         * @param  array @params Associative array of parameters to replace placeholders with.
         * @return string The URL of the route with named parameters in place.
         * @throws Exception
         */
        public function generate($routeName, array $params = [])
        {

            // Check if named route exists
            if (!isset($this->namedRoutes[$routeName])) {
                throw new Exception("Route '{$routeName}' does not exist.");
            }

            // Replace named parameters
            $route = $this->namedRoutes[$routeName];

            // prepend base path to route url again
            $url = $this->basePath.$route;

            if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $index => $match) {
                    list($block, $pre, $type, $param, $optional) = $match;

                    if ($pre) {
                        $block = substr($block, 1);
                    }

                    if (isset($params[$param])) {
                        // Part is found, replace for param value
                        $url = str_replace($block, $params[$param], $url);
                    } elseif ($optional && $index !== 0) {
                        // Only strip preceding slash if it's not at the base
                        $url = str_replace($pre.$block, '', $url);
                    } else {
                        // Strip match block
                        $url = str_replace($block, '', $url);
                    }
                }
            }

            return $url;
        }

        /**
         * Match a given Request Url against stored routes
         * @param  string  $requestUrl
         * @param  string  $requestMethod
         * @return array|boolean Array with route information on success, false on failure (no match).
         */
        public function run($requestUrl = null, $requestMethod = null)
        {

            $params = [];

            // set Request Url if it isn't passed as parameter
            if ($requestUrl === null) {
                $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            }


            // strip base path from request url
            $requestUrl = substr($requestUrl, strlen($this->basePath));

            // Strip query string (?a=b) from Request Url
            if (($strpos = strpos($requestUrl, '?')) !== false) {
                $requestUrl = substr($requestUrl, 0, $strpos);
            }

            $lastRequestUrlChar = $requestUrl ? $requestUrl[strlen($requestUrl) - 1] : '';

            // set Request Method if it isn't passed as a parameter
            if ($requestMethod === null) {
                $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
            }

            foreach ($this->routes as $handler) {

                list($methods, $route, $target, $name) = $handler;

                $method_match = (stripos($methods, $requestMethod) !== false);

                if (!$method_match) {
                    continue;
                }

                if ($route === '*') {
                    $match = true;
                }

                elseif (isset($route[0]) && $route[0] === '@') {
                    // @ regex delimiter
                    $pattern = '`'.substr($route, 1).'`u';
                    $match = preg_match($pattern, $requestUrl, $params) === 1;
                }

                elseif (($position = strpos($route, '[')) === false) {
                    // No params in url, do string comparison
                    $match = strcmp($requestUrl, $route) === 0;
                }

                else {
                    // Compare longest non-param string with url before moving on to regex
                    // Check if last character before param is a slash, because it could be optional if param is optional too (see https://github.com/dannyvankooten/AltoRouter/issues/241)
                    if (strncmp($requestUrl, $route,
                            $position) !== 0 && ($lastRequestUrlChar === '/' || $route[$position - 1] !== '/')) {
                        continue;
                    }

                    $regex = $this->compileRoute($route);
                    $match = preg_match($regex, $requestUrl, $params) === 1;
                }

                if ($match) {

                    if ($params) {
                        foreach ($params as $key => $value) {
                            if (is_numeric($key)) {
                                unset($params[$key]);
                            }
                        }
                    }

                    $target = preg_split('/(:|#)/' , $target);

                    if(empty($target[1])){
                        return false;
                    }

                    return [
                        'class' => $target[0],
                        'func' => $target[1],
                        'params' => $params,
                        'name' => $name
                    ];
                }
            }

            return false;
        }

        /**
         * Compile the regex for a given route (EXPENSIVE)
         * @param $route
         * @return string
         */
        protected function compileRoute($route)
        {
            if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
                $matchTypes = $this->matchTypes;
                foreach ($matches as $match) {
                    list($block, $pre, $type, $param, $optional) = $match;

                    if (isset($matchTypes[$type])) {
                        $type = $matchTypes[$type];
                    }
                    if ($pre === '.') {
                        $pre = '\.';
                    }

                    $optional = $optional !== '' ? '?' : null;

                    //Older versions of PCRE require the 'P' in (?P<named>)
                    $pattern = '(?:'.($pre !== '' ? $pre : null).'('.($param !== '' ? "?P<$param>" : null).$type.')'.$optional.')'.$optional;

                    $route = str_replace($block, $pattern, $route);
                }
            }
            return "`^$route$`u";
        }
    }

