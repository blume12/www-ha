<?php

/**
 * Author: Jasmin Stern
 * Date: 02.01.2017
 * Time: 19:06
 */
namespace App\Model\Reservation;

use App\Helper\Formatter;
use App\Helper\Mail\Mail;
use App\Model\Database\DbBasis;
use App\Model\Program\Program;
use App\Model\TextSource\TextSource;

class Reservation extends DbBasis
{

    /**
     * @var int
     */
    private static $hoursLater = 72;

    /**
     * @var array
     */
    private static $statusArray = [
        'open' => 'offen',
        'expired' => 'Reservierung abgelaufen',
        'confirm' => 'Reservierung bestätigt',
        'paid' => 'bezahlt & abgeholt'
    ];

    /**
     * @return int
     */
    public static function getHoursLater()
    {
        return self::$hoursLater;
    }

    /**
     * @return array
     */
    public static function getStatusArray()
    {
        return self::$statusArray;
    }


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
                SUM(countTickets * price) AS priceTotal, price, status
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE status != 'delete'
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
     * @param bool $groupBy
     * @return array
     */
    public function searchData($value, $groupBy = true)
    {

        $data = [];
        if ($value != '') {
            $dbqObject = $this->getDbqObject();

            $sql = "SELECT 
                    reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                    SUM(countTickets * price) AS priceTotal, price, program.PId, priceMode, countTickets, status
                    FROM reservation 
                    LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                    LEFT JOIN program ON program.PId = reservation_program.PId ";
            if (!$groupBy) {
                $sql .= "WHERE reservation.RId = :value AND status != 'delete' ";
                $sql .= "GROUP BY RPId ";
            } else {
                $sql .= "WHERE (reservationNumber LIKE :value OR firstname LIKE :value OR lastname LIKE :value OR email LIKE :value ) 
                    AND status != 'delete' ";
                $sql .= "GROUP BY reservation.RId";
            }
            $sqlData = ['value' => "%" . $value . "%"];
            if (!$groupBy) {
                $sqlData = ['value' => $value];
            }
            $dbqObject->query($sql, $sqlData);

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
                countTickets, price, program.PId, priceMode, countTickets, status, reservation.text
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE reservation.RId = :RId 
                AND  status != 'delete'
                LIMIT 1 ";
        $dbqObject->query($sql, ['RId' => $id]);

        $data = $dbqObject->nextRow();
        $data['reservationUntil'] = date('d.m.Y H:i', $data['createDate'] + 60 * 60 * self::$hoursLater);
        return $data;
    }

    /**
     * Return a specific reservation data entry per Program.
     *
     * @param $pid
     * @return mixed
     */
    public function loadSpecificEntryPerProgram($pid)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                countTickets, SUM(countTickets * price) AS priceTotal, price, program.PId, priceMode, countTickets, status, reservation.text
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE program.PId = :PId 
                AND  status != 'delete' 
                GROUP BY reservation.RId ";
        $dbqObject->query($sql, ['PId' => $pid]);
        $data = [];
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
            $sql = "INSERT INTO reservation (reservationNumber, firstname,lastname, email, createDate, status) 
                    VALUES (:reservationNumber, :firstname, :lastname, :email, :createDate, :status)";

            $dataSql['createDate'] = time();
        } else {
            $sql = "UPDATE reservation  SET 
                    'reservationNumber' = :reservationNumber,
                    'firstname' = :firstname, 
                    'lastname' = :lastname, 
                    'email' = :email,
                    'status' = :status
                     WHERE RId = :RId ";
            $dataSql['RId'] = intval($data['id'], 10);
        }
        $dataSql['firstname'] = trim($data['firstname']);
        $dataSql['reservationNumber'] = uniqid();
        $dataSql['lastname'] = trim($data['lastname']);
        $dataSql['email'] = trim($data['email']);
        $dataSql['status'] = 'open';
        $dbqObject->query($sql, $dataSql);

        if (!isset($dataSql['RId']) || $dataSql['RId'] == '') {
            $sql = "SELECT last_insert_rowid()";
            $dbqObject->query($sql);
            $dataSql['RId'] = $dbqObject->nextRow()['last_insert_rowid()'];
        }
        $emailData = $dataSql;
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
        $this->sendMail(trim($data['email']), $emailData);
    }

    /**
     * Save data in the backend.
     *
     * @param $data
     */
    public function saveDataInBackend($data)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $sql = "UPDATE reservation SET 
                'status' = :status,
                'text' = :text
                 WHERE RId = :RId ";

        $dataSql['RId'] = intval($data['id'], 10);

        $dataSql['status'] = $data['status'];
        $dataSql['text'] = trim($data['text']);
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Send The mail for the reservation.
     *
     * @param $email
     * @param $data
     */
    private function sendMail($email, $data)
    {
        $textSource = new TextSource($this->getConfig());
        // TODO: not only use the first text source.
        //  if ($textSource->loadData() != false) {
        $textSourceData = $textSource->loadData()[0];
        $mail = new Mail();
        $mail->setSubject($textSourceData['title']);
        $mail->setEmail($email);
        $mail->setMessage($textSource->getConvertedText($textSourceData['text'], $data));
        $mail->sendMail();
        //  }
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

        //$sql = "DELETE FROM reservation WHERE RId=:RId ";
        $sql = "UPDATE reservation SET status = 'delete' WHERE RId=:RId ";

        $dataSql['RId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Return the reservation data by the reservation number.
     *
     * @param $reservationNumber
     * @return mixed
     */
    public function loadSpecificEntryByReservationNumber($reservationNumber)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate
                FROM reservation 
                WHERE reservation.reservationNumber = :reservationNumber AND status IS NOT 'delete'
                LIMIT 1 ";
        $dbqObject->query($sql, ['reservationNumber' => $reservationNumber]);

        $data = $dbqObject->nextRow();
        $data['reservationUntil'] = date('d.m.Y H:i', $data['createDate'] + 60 * 60 * self::$hoursLater);
        $data['reservationExpired'] = $data['createDate'] + 60 * 60 * self::$hoursLater < time();
        return $data;
    }

    /**
     * Save the reservation as confirm.
     *
     * @param $reservationNumber
     */
    public function saveAsConfirm($reservationNumber)
    {
        $this->saveStatus($reservationNumber, 'confirm');
    }

    /**
     * Save the reservation as expired.
     *
     * @param $reservationNumber
     */
    public function saveAsExpired($reservationNumber)
    {
        $this->saveStatus($reservationNumber, 'expired');
    }

    /**
     * Save the status of a reservation by the reservation number.
     *
     * @param $reservationNumber
     * @param $status
     */
    private function saveStatus($reservationNumber, $status)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];

        $sql = "UPDATE reservation SET status = :status WHERE reservationNumber=:reservationNumber AND status = 'open'";

        $dataSql['reservationNumber'] = $reservationNumber;
        $dataSql['status'] = $status;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Return the count of reservation by the given status.
     *
     * @param $status
     * @return int
     */
    public function getCountReservationByStatus($status)
    {
        $dbqObject = $this->getDbqObject();
        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                SUM(countTickets * price) AS priceTotal, price
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE status != 'delete' ";
        $sqlData = [];
        if ($status != 'all') {
            $sql .= "AND status = :status ";
            $sqlData = ['status' => $status];
        }
        $sql .= "GROUP BY reservation.RId";
        $dbqObject->query($sql, $sqlData);
        return $dbqObject->numberOfRows();

    }

    /**
     * @param $pid
     * @return int
     */
    public function getCountReservationByProgram($pid = null)
    {
        $dbqObject = $this->getDbqObject();
        $sql = "SELECT 
                reservation.RId, firstname, lastname, reservationNumber, email, createDate, 
                SUM(countTickets * price) AS priceTotal, price, SUM(countTickets) AS countTickets
                FROM reservation 
                LEFT JOIN reservation_program ON reservation.RId = reservation_program.RId
                LEFT JOIN program ON program.PId = reservation_program.PId
                WHERE status != 'delete' AND status != 'expired' ";
        $dataSql = [];
        if($pid != null) {
            $dataSql = ['PId' => intval($pid, 10)];
            $sql .= " AND program.PId = :PId ";
        }
        $sql .= "GROUP BY reservation.RId";
        $dbqObject->query($sql, $dataSql);
        $count = 0;
        while ($row = $dbqObject->nextRow()) {
            $count += $row['countTickets'];
        }
        return $count;
    }

    /**
     * @param $pid
     * @return int
     */
    public function getCountToReserveActually($pid)
    {
        return Program::getMaxReservationPerProgram() - $this->getCountReservationByProgram($pid);
    }

}