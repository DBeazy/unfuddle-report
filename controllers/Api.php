<?php
namespace UnfuddleReport\Controllers;

use \Curl\Curl;

abstract class Api
{

    protected static $save_key;
    
    private static $base_url;
    private static $username;
    private static $password;

    /**
     * Set the base_url in this class
     *
     * @param $base_url
     */
    final public static function setBaseUrl($base_url)
    {
        self::$base_url = $base_url;
    }

    /**
     * Set the username in this class
     *
     * @param $username
     */
    final public static function setUsername($username)
    {
        self::$username = $username;
    }

    /**
     * Set the password in this class
     *
     * @param $password
     */
    final public static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * Get the endpoint passed from curl
     * 
     * @param $endpoint
     * @return null
     * @throws \Exception
     */
    final protected static function get($endpoint)
    {

        // Cannot make a curl request without a base_url
        if (empty(self::$base_url)) throw new \Exception('You must call Api::setBaseUrl() before calling Api::get().');

        // Create a new curl instance
        $curl = new Curl();

        // Set the authentication of the curl request
        $curl->setBasicAuthentication(strtolower(self::$username), self::$password);

        // Set other default options
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);

        // Get the endpoint passed
        $curl->get(self::$base_url . $endpoint);

        // If there is a curl error, throw an exception.
        if ($curl->error) {
            throw new \Exception('Curl Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
        } else {
            // Get the response
            $response = $curl->response;

            // Close the connection now
            $curl->close();

            // Return the response as is
            return $response;
        }
    }

    /**
     * Set the list into session
     *
     * @param array $list
     * @return bool
     */
    protected static function save_list($list)
    {
        // Check that the save key is set
        if (!empty(static::$save_key)) {
            $_SESSION[static::$save_key] = json_encode($list);
            return true;
        }
        return false;
    }

    /**
     * Check that the api code is set
     *
     * @return bool
     */
    protected static function has_list()
    {
        // Check that the save_key is set
        if (!empty(static::$save_key)) {
            return !empty($_SESSION[static::$save_key]);
        }
        return false;
    }

    /**
     * Get the api list
     *
     * @return mixed
     */
    protected static function get_list()
    {
        // Check that the save_key is set
        if (!empty(static::$save_key) && self::has_list()) {
            return json_decode($_SESSION[static::$save_key], true);
        }
        return false;
    }

    /**
     * Get the base url
     */
    protected static function getBaseUrl()
    {
        return self::$base_url;
    }

}
