<?php

/**
 * Author: Jasmin Stern
 * Date: 24.11.2016
 * Time: 20:13
 */
namespace App\Helper;

class Session
{
    /**
     * Start the session.
     */
    public static function startSession()
    {
        session_start();
    }


    /**
     * Return the session by the key.
     *
     * @param $key
     * @return bool
     */
    public static function getSessionByKey($key)
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }
        return $_SESSION[$key];
    }

    /**
     * Set a session.
     *
     * @param $key
     * @param $value
     */
    public static function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Remove the session.
     */
    public static function removeSession()
    {
        session_destroy();
    }

}