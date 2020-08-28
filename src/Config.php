<?php

declare(strict_types=1);

namespace src\Config;

use src\Environment\Environment;

/**
 * Class Environment
 * @package src\Environment
 */
class Config
{

    public function __construct()
    {

        $environment = new Environment();

        $class_vars = get_class_vars(get_class($environment));

        foreach ($class_vars as $name => $value) {

            $this->set($name, $value);

        }

    }

    /**
     * @param $name
     * @param $value
     */
    private function set($name, $value) : void
    {

        define(strtoupper($name), $value);

    }

}