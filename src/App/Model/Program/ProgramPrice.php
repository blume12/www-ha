<?php
/**
 * User: Jasmin
 * Date: 09.12.2016
 */

namespace App\Model\Program;


use App\Helper\Validator;
use App\Model\Database\DbBasis;

class ProgramPrice extends DbBasis
{
    /**
     * Return the data array of all program prices.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT PPId, name, price, priceReduce FROM programPrice ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[$row['PPId']] = $row;
            $data[$row['PPId']]['index'] = $i;
            $i++;
        }

        return $data;
    }

    /**
     * Return a specific program price data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $sql = "SELECT PPId, name, price, priceReduce FROM programPrice WHERE PPId = :PPId LIMIT 1 ";
        $dbqObject->query($sql, ['PPId' => $id]);
        return $dbqObject->nextRow();
    }

    /**
     * Save a program price data. It decide if it will do a update or a insert.
     *
     * @param $data
     */
    public function saveData($data)
    {
        $dbqObject = $this->getDbqObject();
        $dataSql = [];
        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO programPrice ( name, price, priceReduce) VALUES (:name, :price, :priceReduce)";
        } else {
            $sql = "UPDATE programPrice  SET 'name' = :name, 'price' = :price, priceReduce = :priceReduce WHERE PPId = :PPId ";
            $dataSql['PPId'] = intval($data['id'], 10);
        }
        $dataSql['name'] = trim($data['name']);
        $dataSql['price'] = trim($data['price']);
        $dataSql['priceReduce'] = trim($data['priceReduce']);
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

        $sql = "DELETE FROM programPrice WHERE PPId=:PPId ";

        $dataSql['PPId'] = $id;
        $dbqObject->query($sql, $dataSql);
    }

    /**
     * Check Errors for the form data of a program price.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        $formError = [];
        if (!Validator::isAlpha($formData['name'], true)) {
            $formError['name'] = 'Bitte geben Sie einen Namen.';
        }
        if (!Validator::isPrice($formData['price'], true)) {
            $formError['price'] = 'Bitte geben Sie einen normalen Preis an.';
        }
        if (!Validator::isPrice($formData['priceReduce'], true)) {
            $formError['priceReduce'] = 'Bitte geben Sie einen reduzierten Preis an.';
        }
        return $formError;
    }

    /**
     * Get the price by a mode.
     *
     * @param $mode
     * @param $priceId
     * @return int
     */
    public function getPriceByMode($mode, $priceId)
    {
        // TODO: the mode should be a string mode
        $programPriceData = $this->loadSpecificEntry($priceId);
        switch ($mode) {
            case 0:
                $simplePrice = $programPriceData['price'];
                break;
            case 1:
                $simplePrice = $programPriceData['priceReduce'];
                break;
            default:
                $simplePrice = 3; // TODO: This is a very hot fix for tomorrow
                break;
        }

        return $simplePrice;
    }
}