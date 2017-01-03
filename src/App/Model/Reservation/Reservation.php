<?php

/**
 * Author: Jasmin Stern
 * Date: 02.01.2017
 * Time: 19:06
 */
namespace App\Model\Reservation;

use App\Helper\Formatter;
use App\Model\Database\DbBasis;

class Reservation extends DbBasis
{

    private static $hoursLater = 72;

    /**
     * Return the data array of all reservation.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                SUM(countTickets * price) AS priceTotal, price
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                GROUP BY reservation.RId";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            $data[$i]['reservationUntil'] = date('d.m.Y H:i', $data[$i]['createDate'] + 60 * 60 * self::$hoursLater);
            $data[$i]['priceTotal'] = Formatter::formatPrice($data[$i]['priceTotal']);
            $i++;
        }

        return $data;
    }

    /**
     * @param $value
     * @return array
     */
    public function searchData($value)
    {

        $data = [];
        if ($value != '') {
            $dbqObject = $this->getDbqObject();

            $sql = "SELECT 
                    reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                    SUM(countTickets * price) AS priceTotal, price
                    FROM reservation 
                    LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                    LEFT JOIN program ON program.PId = reservation_program.PId
                    WHERE reservationNumber LIKE :value OR firstname LIKE :value OR lastname LIKE :value OR email LIKE :value 
                    GROUP BY reservation.RId";
            $dbqObject->query($sql, ['value' => "%" . $value . "%"]);

            $i = 0;
            while ($row = $dbqObject->nextRow()) {
                $data[$i] = $row;
                $data[$i]['index'] = $i;
                $data[$i]['reservationUntil'] = date('d.m.Y H:i', $data[$i]['createDate'] + 60 * 60 * self::$hoursLater);
                $data[$i]['priceTotal'] = Formatter::formatPrice($data[$i]['priceTotal']);
                $i++;
            }
        } else {
            $data = $this->loadData();
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

        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                countTickets, price
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE reservation.RId = :RId 
                LIMIT 1 ";
        $dbqObject->query($sql, ['RId' => $id]);

        $data = $dbqObject->nextRow();
        $data['reservationUntil'] = date('d.m.Y H:i', $data['createDate'] + 60 * 60 * self::$hoursLater);
        return $data;
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
            $sql = "INSERT INTO reservation (reservationNumber, firstname,lastname, email, createDate) 
                    VALUES (:reservationNumber, :firstname, :lastname, :email, :createDate)";
        } else {
            $sql = "UPDATE reservation  SET 
                    'reservationNumber' = :reservationNumber,
                    'firstname' = :firstname, 
                    'lastname' = :lastname, 
                    'email' = :email,
                    'createDate' = :createDate
                     WHERE RId = :RId ";
            $dataSql['RId'] = intval($data['id'], 10);
        }
        $dataSql['firstname'] = trim($data['firstname']);
        $dataSql['reservationNumber'] = uniqid();
        $dataSql['lastname'] = trim($data['lastname']);
        $dataSql['email'] = trim($data['email']);
        $dataSql['createDate'] = time();
        $dbqObject->query($sql, $dataSql);

        if (!isset($dataSql['RId']) || $dataSql['RId'] == '') {
            $sql = "SELECT last_insert_rowid()";
            $dbqObject->query($sql);
            $dataSql['RId'] = $dbqObject->nextRow()['last_insert_rowid()'];
        }
        $rid = $dataSql['RId'];

        foreach ($data['program'] as $programData) {
            $sql = "INSERT INTO reservation_program (PId, RId, priceMode,countTickets, price) 
                    VALUES (:PId, :RId, :priceMode, :countTickets, :price)";
            $dataSql = [];
            $dataSql['RId'] = $rid;
            $dataSql['PId'] = trim($programData['PId']);
            $dataSql['priceMode'] = $programData['priceMode'];
            $dataSql['countTickets'] = trim($programData['countTickets']);
            $dataSql['price'] = trim($programData['price']);
            $dbqObject->query($sql, $dataSql);
        }
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