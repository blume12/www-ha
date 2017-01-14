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
     * @var array
     */
    private static $status = [
        'active' => 'aktiviert',
        'notActive' => 'deaktiviert'
    ];

    /**
     * Return the status array.
     *
     * @param null $status
     * @return array|mixed
     */
    public static function getStatus($status = null)
    {
        if ($status == null) {
            return self::$status;
        }
        return self::$status[$status];
    }

    /**
     * Return the data array of all textSources.
     *
     * @param bool $activeStatus
     * @return array
     */
    public function loadData($activeStatus = false)
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT TSId, title, text, status FROM textSource ";
        if ($activeStatus) {
            $sql .= 'WHERE status = "active" LIMIT 1';
        }
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

        $sql = "SELECT TSId, title, text, status FROM textSource WHERE TSId = :TSId LIMIT 1 ";
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

        if ($data['status'] == 'active') {
            $sql = "UPDATE textSource  SET 'status' = 'notActive'  ";
            $dbqObject->query($sql);
        }

        $dataSql = [];
        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO textSource ( title,text, status) VALUES (:title, :text, :status)";
        } else {
            $sql = "UPDATE textSource  SET 'title' = :title, 'text' = :text , 'status' = :status WHERE TSId = :TSId ";
            $dataSql['TSId'] = intval($data['id'], 10);
        }
        $dataSql['title'] = trim($data['title']);
        $dataSql['status'] = trim($data['status']);
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
     * @param $newEntry
     * @return array
     */
    public function checkErrors($formData, $newEntry)
    {
        // TODO: check all the data of a program
        $formError = [];

        if (!Validator::isAlpha($formData['title'], true)) {
            $formError['title'] = 'Bitte geben Sie einen Titel an.';
        }
        if (!Validator::isAlpha($formData['text'], true)) {
            $formError['text'] = 'Bitte geben Sie einen Text an.';
        }
        $count = count($this->loadData());
        if ((!$newEntry && $count <= 1) || ($newEntry && $count <= 0) && $formData['status'] != 'active') {
            $formError['status'] = 'Es gibt nur einen Eintrag. Dieser Eintrag muss den Status "aktiviert" sein.';
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
    public function getConvertedText($text, $data)
    {
        $text = preg_replace('/\(% vorname %\)/', $data['firstname'], $text);
        $text = preg_replace('/\(% nachname %\)/', $data['lastname'], $text);
        $text = preg_replace('/\(% reservierungsnummer %\)/', $data['reservationNumber'], $text);
        //TODO: Use getRoutePath ;)
        $link = $this->getMainUrl() . 'reservierung-bestaetigen/' . $data['reservationNumber'];
        $text = preg_replace('/\(% link %\)/', $link, $text);
        return $text;
    }
}