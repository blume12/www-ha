<?php

/**
 * Author: Jasmin Stern
 * Date: 02.01.2017
 * Time: 19:06
 */
namespace App\Model\Reservation;

use App\Helper\Validator;
use App\Model\Database\DbBasis;

class Reservation extends DbBasis
{

    /**
     * Return the data array of all reservation.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT RId, firstname, lastname, reservationNumber,email FROM reservation ";
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
     * Return a specific reservation data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT RId, firstname, lastname, reservationNumber, email FROM reservation WHERE RId = :RId LIMIT 1 ";
        $dbqObject->query($sql, ['RId' => $id]);
        return $dbqObject->nextRow();
    }

    /**
     * Save a reservation data. It decide if it will do a update or a insert.
     *
     * @param $data
     */
    public function saveData($data)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $entry = false;
        if (isset($data['id'])) {
            $entry = $this->loadSpecificEntry($data['id']);
        }
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO reservation (reservationNumber, firstname,lastname, email) VALUES (:reservationNumber, :firstname, :lastname, :email)";
        } else {
            $sql = "UPDATE reservation  SET 'reservationNumber' = :reservationNumber,'firstname' = :firstname, 'lastname' = :lastname, 'email' = :email WHERE RId = :RId ";
            $dataSql['RId'] = intval($data['id'], 10);
        }
        $dataSql['firstname'] = trim($data['firstname']);
        $dataSql['reservationNumber'] = uniqid();
        $dataSql['lastname'] = trim($data['lastname']);
        $dataSql['email'] = trim($data['email']);
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

        $sql = "DELETE FROM reservation WHERE RId=:RId ";

        $dataSql['RId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Check Errors for the form data of a reservation.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        // TODO: check all the data of a program
        $formError = [];
        return $formError;
    }

}