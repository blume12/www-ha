<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 15:58
 */

namespace App\Model\Timescale;


use App\Helper\Validator;
use App\Model\Database\DbBasis;

class Timescale extends DbBasis
{

    /**
     * Return the data array of all timescale.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT TId, year, fromDate, untilDate FROM timescale ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            // TODO: Change links to really routes
            $data[$i]['editRoute'] = '/admin/zeitraum-bearbeiten/' . $row['TId']; // later: adminProgramEdit -> id
            $data[$i]['deleteRoute'] = '/admin/zeitraum-loeschen/' . $row['TId']; // later: adminProgramDelete -> id
            $i++;
        }

        return $data;
    }

    /**
     * Return a specific timescale data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT TId, year, fromDate, untilDate FROM timescale WHERE TId = :TId LIMIT 1 ";
        $dbqObject->query($sql, ['TId' => $id]);
        return $dbqObject->nextRow();
    }

    /**
     * Save a timescale data. It decide if it will do a update or a insert.
     *
     * @param $data
     */
    public function saveData($data)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO timescale ( year,fromDate, untilDate) VALUES (:year, :fromDate, :untilDate)";
        } else {
            $sql = "UPDATE timescale  SET 'year' = :year, 'fromDate' = :fromDate, untilDate = :untilDate WHERE TId = :TId ";
            $dataSql['TId'] = intval($data['id'], 10);
        }
        $dataSql['year'] = trim($data['year']);
        $dataSql['fromDate'] = trim($data['fromDate']);
        $dataSql['untilDate'] = trim($data['untilDate']);
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

        $sql = "DELETE FROM timescale WHERE TId=:TId ";

        $dataSql['TId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Check Errors for the form data of a timescale.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        $formError = [];
        if (Validator::isYear($formData['year'], true)) {
            $formError['year'] = 'Bitte geben Sie ein Jahr an. Format: JJJJ';
        }
        if (Validator::isDate($formData['fromDate'], true)) {
            $formError['fromDate'] = 'Bitte geben Sie ein Datum (ab) an. Format: tt.mm.JJJJ';
        }
        if (Validator::isDate($formData['untilDate'], true)) {
            $formError['untilDate'] = 'Bitte geben Sie ein Datum (bis) an.';
        }
        return $formError;
    }
}