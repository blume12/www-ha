<?php

/**
 * User: Jasmin
 * Date: 25.11.2016
 */
namespace App\Model\Program;

use App\Helper\FileDirectory\FileUpload;
use App\Helper\Validator;
use App\Model\Database\DbBasis;

class Program extends DbBasis
{

    /**
     * @var null | FileUpload
     */
    private $fileUpload = null;

    /**
     * @var int
     */
    private static $placesPerProgram = 60;
    /**
     * @var array
     */
    private $currentData = [];

    /**
     * The path to the image folder for the programs.
     * @var string
     */
    private $imagePath = 'program';

    /**
     * Api to the programs from the existing ticket system.
     * @var string
     */
    private static $api = 'http://kft.mi.fh-flensburg.de/ojupardeurcawedbertorbyviflenyt/programme';

    /**
     * Load Json and save to DB.
     */
    public function loadJSON()
    {
        $content = file_get_contents(self::$api);
        $jsonContent = json_decode($content, true);
        foreach ($jsonContent as $key => $value) {
            $data = [
                'userid' => null,
                'price' => null,
                'author' => null,
                'countTickets' => null,
                'date' => $value['datum'],
                'title' => $value['titel'],
                'text' => $value['einleitung']
            ];
            $this->saveData($data);
        }
    }

    /**
     * Init the image upload.
     *
     * @param $fileData
     */
    public function initImageUpload($fileData)
    {
        $this->fileUpload = new FileUpload($fileData);
    }


    /**
     * Return the data array of all programs.
     *
     * @param bool $forFrontend
     * @param bool $limit
     * @return array
     */
    public function loadData($forFrontend = false, $limit = false)
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $dataSql = [];
        $sql = "SELECT program.PId, BUId, author, date, title, text, PPId, countTickets ";
        $sql .= "FROM program ";
        if (!$this->isFrontend() && !$forFrontend) {
            $sql .= "LEFT ";
        }
        $sql .= "JOIN program_programPrice ON program.PId = program_programPrice.PId ";
        $sql .= 'GROUP BY program.PId ';
        if (!$this->isFrontend() && !$forFrontend) {
            $sql .= 'ORDER BY PPId DESC ';
        } else if ($limit != false) {
            $sql .= "ORDER BY program.PId DESC ";
            $sql .= "LIMIT :limit";
            $dataSql = ['limit' => intval($limit, 10)];
        }

        $dbqObject->query($sql, $dataSql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$i] = $row;
            $data[$i]['index'] = $i;
            $data[$i]['image'] = $this->getImageForOutput($row['PId'] . '_program');
            $i++;
        }
        $this->currentData = $data;

        return $data;
    }

    /**
     * Get the number of all not visible programs.
     *
     * @return int
     */
    public function getCountOfNotVisiblePrograms()
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT program.PId, BUId, author, date, title, text, countTickets FROM program 
                LEFT JOIN program_programPrice ON program.PId = program_programPrice.PId 
                WHERE PPId IS NULL
                GROUP BY program.PId ";
        $dbqObject->query($sql);

        return $dbqObject->numberOfRows();
    }

    /**
     * Return a specific program data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT program.PId, program_programPrice.PPId AS price, BUId, author, date, title, text, countTickets,
                programPrice.price AS priceNormal,  programPrice.priceReduce       
                FROM program 
                LEFT JOIN program_programPrice ON program.PId = program_programPrice.PId 
                LEFT JOIN programPrice ON program_programPrice.PPId = programPrice.PPId 
                WHERE program.PId = :PId LIMIT 1 ";
        $dbqObject->query($sql, ['PId' => $id]);

        $row = $dbqObject->nextRow();
        if (count($row) > 0) {
            $row['image'] = $this->getImageForOutput($row['PId'] . '_program');
        }
        return $row;
    }


    /**
     * Save a program data. It decide if it will do a update or a insert.
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
            $sql = "INSERT INTO program ( BUId,author,date,title, text, countTickets)
                              VALUES (:BUId, :author, :date, :title, :text, :countTickets)";

            $dataSql['date'] = time();
        } else {
            $sql = "UPDATE program  SET 'BUId' = :BUId, 'author' = :author, 'title' = :title,
                    'text' = :text, countTickets = :countTickets WHERE PId = :PId ";
            $dataSql['PId'] = intval($data['id'], 10);
        }

        // TODO: fix the data author
        $dataSql['BUId'] = $data['userid'];
        $dataSql['author'] = null;
        $dataSql['title'] = $data['title'];
        $dataSql['text'] = $data['text'];
        $dataSql['countTickets'] = $data['countTickets'];
        $dbqObject->query($sql, $dataSql);
        if (!isset($dataSql['PId']) || $dataSql['PId'] == '') {
            $sql = "SELECT last_insert_rowid()";
            $dbqObject->query($sql);
            $dataSql['PId'] = $dbqObject->nextRow()['last_insert_rowid()'];
        }
        if ($data['price'] != null) {
            $this->savePriceToProgram($dataSql['PId'], $data['price']);

        }
        if ($this->fileUpload != null) {
            $this->fileUpload->saveFile('program', $dataSql['PId'] . '_program', 600, 'jpg');
        }
    }

    /**
     * Get the path for the image for the output.
     *
     * @param $fileName
     * @return string
     */
    public function getImageForOutput($fileName)
    {
        $file = '/data/' . $this->imagePath . '/' . $fileName . '.jpg';
        if (file_exists(realpath(dirname(__FILE__) . '/../../../../public' . $file))) {
            return $file;
        }

        return false;
    }

    /**
     * Save the prices in a specific table for the program.
     *
     * @param $pid
     * @param $ppid
     */
    public function savePriceToProgram($pid, $ppid)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $dataSql['PId'] = intval($pid, 10);
        $sql = "DELETE FROM  program_programPrice WHERE PId = :PId ";
        $dbqObject->query($sql, $dataSql);

        $sql = "INSERT INTO program_programPrice ( PId,PPId) VALUES (:PId, :PPId)";
        $dataSql['PPId'] = intval($ppid, 10);
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

        $sql = "DELETE FROM program WHERE PId=:PId ";

        $dataSql['PId'] = $id;
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
        $formError = [];

        if (!Validator::isAlpha($formData['title'], true)) {
            $formError['title'] = 'Bitte geben Sie einen Titel an.';
        }
        if (!Validator::isCorrectSelectValue($formData['price'], true)) {
            $formError['price'] = 'Bitte geben Sie einen Preis an.';
        }
        if (!Validator::isInteger($formData['countTickets'])) {
            $formError['countTickets'] = 'Bitte geben Sie eine Anzahl der verfügbaren Plätze an.';
        }

        if (!Validator::isAlpha($formData['text'], true)) {
            $formError['text'] = 'Bitte geben Sie einen Text an.';
        }

        return $formError;
    }

    /**
     * Return all programs.
     *
     * @return int
     */
    public function getCountOfPrograms()
    {
        $programData = $this->loadData(true);
        return count($programData);
    }

    /**
     * Return the number of all Places of the shop.
     *
     * @return int
     */
    public function getCountOfAllPlaces()
    {

        $programData = $this->loadData(true);
        $countTickets = 0;
        foreach ($programData as $key => $data) {
            if ($data['countTickets'] == null) {
                $countTickets += self::$placesPerProgram;
            } else {
                $countTickets += $data['countTickets'];
            }
        }
        return $countTickets;
    }

    /**
     * @return int
     */
    public static function getMaxReservationPerProgram()
    {
        return self::$placesPerProgram;
    }
}