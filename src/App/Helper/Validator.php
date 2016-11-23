<?php
/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 07.11.2016
 * Time: 22:21
 */

namespace App\Helper;


class Validator
{
    /**
     * Return if the string is a alpha string. If required is true, the text must not be empty.
     *
     * @param $text
     * @param bool $required
     * @return bool
     */
    public static function isAlpha($text, $required = false)
    {
        $result = true;
        if ($required && $text != '' && $text != null) {
            $result = false;
        }
        return $result;
    }

}