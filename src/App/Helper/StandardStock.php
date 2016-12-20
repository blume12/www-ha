<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 21:18
 */

namespace App\Helper;


class StandardStock
{
    /**
     * @var array
     */
    private static $appellation = [
        'mr' => 'Herr',
        'mrs' => 'Frau'
    ];

    /**
     * Return the array if the parameter is null. Otherwise it return the appellation value to a key.
     *
     * @param null $appellation
     * @return array|mixed
     */
    public static function getAppellation($appellation = null)
    {
        if ($appellation == null) {
            return self::$appellation;
        }
        return self::$appellation[$appellation];
    }

    public static function getCountOfTickets()
    {
        $countOfTickets = [];
        for ($i = 0; $i <= 10; $i++) {
            $countOfTickets[$i] = $i;
        }
        return $countOfTickets;
    }
}