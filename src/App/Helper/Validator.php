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

    /**
     * Return if it's a price. If required is true, the price could not be empty.
     *
     * @param $price
     * @param bool $required
     * @return bool
     */
    public static function isPrice($price, $required = false)
    {
        $pattern = '/^\d+(?:\,\d{2})?$/';

        $result = true;
        if ($required && !preg_match($pattern, $price)) {
            $result = false;
        }
        return $result;
    }

    /**
     * Return if it's correct selected item. If required is true, the select could not be empty.
     *
     * @param $value
     * @param bool $required
     * @return bool
     */
    public static function isCorrectSelectValue($value, $required = false)
    {
        $result = true;
        if ($required && $value == 'notset') {
            $result = false;
        }
        return $result;
    }

    /**
     * Return if it's correct email. If required is true, the value could not be empty.
     *
     * @param $email
     * @param bool $required
     * @return bool
     */
    public static function isEmail($email, $required = false)
    {
        $result = true;
        if ($required && $email == '') {
            $result = false;
        } else {
            $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            if ((!$required && $email != '' && !$isEmail) || ($required && !$isEmail)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @param $value
     * @param bool $required
     * @return bool
     */
    public static function isInteger($value, $required = false)
    {

        $result = true;
        if ($required && $value == '') {
            $result = false;
        } else {
            if (!is_numeric($value) && (!$required && $value != '') || ($required)) {
                $result = false;
            }
        }
        return $result;
    }
}