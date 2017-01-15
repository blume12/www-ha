<?php
/**
 * Author: Jasmin Stern
 * Date: 21.12.2016
 * Time: 22:48
 */

namespace App\Helper;


class Formatter
{

    /**
     * Format a price to a german format.
     *
     * @param $price
     * @return string
     */
    public static function formatPrice($price)
    {
        return number_format(intval($price, 10), 2, ',', '.') . ' €';
    }
}