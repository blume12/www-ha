<?php

/**
 * User: Jasmin
 * Date: 25.11.2016
 */
namespace App\Model\Program;

use App\Helper\Validator;
use App\Model\Database\DbBasis;

class Program extends DbBasis
{

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
                'id' => $value['nid'],
                'uuid' => null,
                'author' => null,
                'date' => $value['datum'],
                'title' => $value['titel'],
                'intro' => null,
                'text' => $value['einleitung']
            ];
            $this->saveData($data);
        }
    }

    /**
     * Return the data array of all programs.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT PId, uuid, author, date, title, intro, text FROM program ";
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
     * Return a specific program data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT program.PId, program_programPrice.PPId AS price, uuid, author, date, title, intro, text FROM program 
                LEFT JOIN program_programPrice ON program.PId = program_programPrice.PId 
                WHERE program.PId = :PId LIMIT 1 ";
        $dbqObject->query($sql, ['PId' => $id]);
        return $dbqObject->nextRow();
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
        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO program ( uuid,author,date,title,intro, text)
                              VALUES (:uuid, :author, :date, :title, :intro, :text)";
        } else {
            $sql = "UPDATE program  SET 'uuid' = :uuid, 'author' = :author, 'date' = :date, 'title' = :title,
                    'intro' = :intro, 'text' = :text WHERE PId = :PId ";
            $dataSql['PId'] = intval($data['id'], 10);
        }

        $dataSql['uuid'] = $data['uuid'];
        $dataSql['author'] = $data['author'];
        $dataSql['date'] = $data['date'];
        $dataSql['title'] = $data['title'];
        $dataSql['intro'] = $data['intro'];
        $dataSql['text'] = $data['text'];
        $dbqObject->query($sql, $dataSql);
        if (!isset($dataSql['PId']) || $dataSql['PId'] == '') {
            $sql = "SELECT last_insert_rowid()";
            $dbqObject->query($sql);
            $dataSql['PId'] = $dbqObject->nextRow()['last_insert_rowid()'];
        }
        $this->savePriceToProgram($dataSql['PId'], $data['price']);
    }


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
        // TODO: check all the data of a program
        $formError = [];

        if (!Validator::isAlpha($formData['title'], true)) {
            $formError['title'] = 'Bitte geben Sie einen Titel an.';
        }
        if (!Validator::isAlpha($formData['intro'], true)) {
            $formError['intro'] = 'Bitte geben Sie einen Intro an.';
        }
        if (!Validator::isCorrectSelectValue($formData['price'], true)) {
            $formError['price'] = 'Bitte geben Sie einen Preis an.';
        }
        if (!Validator::isAlpha($formData['text'], true)) {
            $formError['text'] = 'Bitte geben Sie einen Text an.';
        }
        return $formError;
    }
}