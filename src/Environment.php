<?php

declare(strict_types=1);

namespace src\Environment;

use Exception;

/**
 * Class Environment
 * @package src\Environment
 */
class Environment
{

    public $App = array(
        'Name' => '',
        'Url' => '',
        'Locale' => 'en',
        'Version' => '1.0'
    );

    public $Domain = array(
        'Cookie' => '',
        'Session' => '',
        'MemoryLimit' => '256'
    );

    public $Log = array(
        'Debug' => false,
        'Path' => '/src/Log',
        'DisplayErrors' => false
    );

    public $Database = array(
        'Host' => 'localhost',
        'User' => 'root',
        'Password' => '',
        'tableName' => 'sitebendorf'
    );

    public $Encryption = array(
        'Salt' => '',
        'AuthKey' => '',
    );

    public $Dir = array(
        'Log' => '',
        'Entity' => '',
        'Public' => '',
        'Controller' => '',
        'Migrations' => '',
        'Providers' => ''
    );


    /**
     * Get environment variable
     *
     * @param $type
     * @param $property
     *
     * @return mixed
     *
     * @throws Exception
     */
    static function getenv($type, $property)
    {

        if (self::checkenv($property, $type)) {

            return $property[$type];

        }

        return false;

    }

    /**
     *
     * Check if the environment variable exists
     *
     * @param $property
     * @param $type
     * @return bool
     *
     * @throws Exception
     *
     */
    static function checkenv($property, $type)
    {

        if (array_key_exists($property, $type)) {
            return true;
        }

        throw new Exception('Non-existent environment variable.');

    }

    /**
     * Update environment variable
     *
     * @param $property
     * @param $type
     * @param $value
     *
     * @return mixed
     *
     * @throws Exception
     *
     */
    static function putenv($property, $type, $value)
    {

        if (self::checkenv($property, $type)) {

            $property[$type] = $value;

            return $property[$type];

        }

        return false;

    }

    /**
     * Remove environment value
     *
     * @param $property
     * @param $type
     *
     * @return bool
     *
     * @throws Exception
     *
     */
    static function clrenv($property, $type)
    {

        if (self::checkenv($property, $type)) {

            unset($property[$type]);

        }

        return false;

    }

}