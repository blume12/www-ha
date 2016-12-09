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
        if ($required && ($text == '' || $text == null)) {
            $result = false;
        }
        return $result;
    }

    /**
     * Return if it's a year. If required is true, the year could not be empty.
     *
     * @param $date
     * @param bool $required
     * @return bool
     */
    public static function isYear($date, $required = false)
    {
        $result = true;
        if ($required && !Date::isYear($date)) {
            $result = false;
        }
        return $result;
    }

    /**
     * Return if it's a date. If required is true, the date could not be empty.
     *
     * @param $date
     * @param bool $required
     * @return bool
     */
    public static function isDate($date, $required = false)
    {
        $result = true;
        if ($required && (!Date::isDate($date, 'd.m.y') && !Date::isDate($date, 'j.n.Y')
                && !Date::isDate($date, 'd.n.Y') && !Date::isDate($date, 'j.m.Y'))
        ) {
            $result = false;
        }
        return $result;
    }

    public static function isPrice($price, $required = false)
    {
        // TODO: do it in english format
        $pattern = '/^\d+(?:\,\d{2})?$/';

        $result = true;
        if ($required && !preg_match($pattern, $price)) {
            $result = false;
        }
        return $result;
    }
}