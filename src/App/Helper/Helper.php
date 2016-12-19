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

    /**
     * Return a array with the error messages for a delete action.
     *
     * @param $data
     * @return array
     */
    public static function checkErrorForDelete($data)
    {
        $formError = [];
        if (!isset($data['safetyDelete']) || $data['safetyDelete'] == null || $data['safetyDelete'] != 'sure') {
            $formError['safetyDelete'] = "Sie müssen das Löschen bestätigen.";
        }
        return $formError;
    }

    /**
     * Limit a text to max Words.
     *
     * @param $text
     * @param int $limit
     * @return string
     */
    public static function maxWords($text, $limit = 20)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . ' ...';
        }
        return $text;
    }

}