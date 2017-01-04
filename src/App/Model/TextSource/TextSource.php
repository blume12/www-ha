<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 15:58
 */

namespace App\Model\TextSource;


use App\Helper\Validator;
use App\Model\Database\DbBasis;

class TextSource extends DbBasis
{

    /**
     * Return the data array of all textSources.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT TSId, title, text FROM textSource ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            $i++;
        }

        return $data;
    }

    /**
     * Return a specific textSource data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT TSId, title, text FROM textSource WHERE TSId = :TSId LIMIT 1 ";
        $dbqObject->query($sql, ['TSId' => $id]);
        return $dbqObject->nextRow();
    }

    /**
     * Save a textSource data. It decide if it will do a update or a insert.
     *
     * @param $data
     */
    public function saveData($data)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO textSource ( title,text) VALUES (:title, :text)";
        } else {
            $sql = "UPDATE textSource  SET 'title' = :title, 'text' = :text WHERE TSId = :TSId ";
            $dataSql['TSId'] = intval($data['id'], 10);
        }
        $dataSql['title'] = trim($data['title']);
        $dataSql['text'] = trim($data['text']);
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Delete a data by the id
     *
     * @param $id integer
     */
    public function deleteData($id)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];

        $sql = "DELETE FROM textSource WHERE TSId=:TSId ";

        $dataSql['TSId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Check Errors for the form data of a program.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        // TODO: check all the data of a program
        $formError = [];

        if (!Validator::isAlpha($formData['title'], true)) {
            $formError['title'] = 'Bitte geben Sie einen Titel an.';
        }
        if (!Validator::isAlpha($formData['text'], true)) {
            $formError['text'] = 'Bitte geben Sie einen Text an.';
        }
        return $formError;
    }

    /**
     * Convert a text with the specific values.
     *
     * @param $text
     * @param $data
     * @return mixed
     */
    public static function getConvertedText($text, $data)
    {
        $text = preg_replace('/\(% vorname %\)/', $data['firstname'], $text);
        $text = preg_replace('/\(% nachname %\)/', $data['lastname'], $text);
        $link = 'http://localhost:1235/reservierung-bestaetigen/' . $data['reservationNumber'];
        $text = preg_replace('/\(% link %\)/', $link, $text);
        return $text;
    }
}