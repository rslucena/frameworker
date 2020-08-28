<?php


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
        'Port' => '',
        'Host' => '',
        'User' => '',
        'Password' => '',
        'Database' => '',
        'Prefix' => '',
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
        'Migrations' => ''
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
    public function getenv($type, $property)
    {

        if ($this->checkenv($property, $type)) {

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
    private function checkenv($property, $type)
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
    public function putenv($property, $type, $value)
    {

        if ($this->checkenv($property, $type)) {

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
    public function clrenv($property, $type)
    {

        if ($this->checkenv($property, $type)) {

            unset($property[$type]);

        }

        return false;

    }

}