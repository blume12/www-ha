<?php

/**
 * User: Jasmin Stern (stja7017)
 * Date: 25.10.2016
 * Time: 15:32
 */
namespace App\Helper;

class Helper
{
    /**
     * Escape a string.
     *
     * @param $string
     * @return string
     */
    public static function escapeString($string)
    {
        return htmlspecialchars($string);
    }
}