<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 17:10
 */

namespace App\Helper;


class Date
{
    /**
     * Check, if it's a year.
     *
     * @param $value
     * @return bool
     */
    public static function isYear($value)
    {
        if (strlen($value) == 4) {
            return true;
        }
        return false;
    }

    /**
     * Check, if it's a date by a specific format.
     *
     * @param $date
     * @param string $format
     * @return bool
     */
    public static function isDate($date, $format = 'd.m.Y')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;

    }
}