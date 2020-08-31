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

        $vars = get_class_vars(get_class($environment));

        foreach ($vars as $keys => $properties) {

            foreach ($properties as $key => $value) {

                $this->set($keys . '_' . $key, $value);

            }

        }

    }

    /**
     * @param $name
     * @param $value
     */
    private function set($name, $value) : void
    {

        if (!defined(strtoupper($name))) {
            define(strtoupper($name), $value);
        }

    }

}