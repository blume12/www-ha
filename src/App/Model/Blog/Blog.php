<?php
/**
 * User: Jasmin Stern (stja7017)
 * Date: 06.10.16
 * Time: 10:36
 */
namespace App\Model\Blog;

use App\Model\Database\DbBasis;

require_once(dirname(__FILE__) . '/../../config.php');

class Blog extends DbBasis
{

    /**
     * Return a array with all the blog data.
     *
     * @return array
     */
    public function loadData()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sql = "SELECT id, uuid, author, date, title, intro, text FROM blog ";
        $dbqObject->query($sql);
        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[] = $row;
            $i++;
        }

        return $data;
    }

    /**
     * Return a specific blog data entry.
     *
     * @param $id
     * @return mixed
     */
    public function loadSpecificEntry($id)
    {
        $dbqObject = $this->getDbqObject();

        $data = ['id' => $id];

        $sql = "SELECT id, uuid, author, date, title, intro, text FROM blog WHERE id = :id LIMIT 1 ";
        $dbqObject->query($sql, $data);
        return $dbqObject->nextRow();
    }

    /**
     * Save the data into the db. If the id already exists in the db, it will make a update.
     * @param $data
     */
    public function saveData($data)
    {

        $dbqObject = $this->getDbqObject();

        $entry = $this->loadSpecificEntry($data['id']);
        if ($entry == false || count($entry) <= 0) {
            $sql = "INSERT INTO blog (id,  uuid,author,date,title,intro, text)
                              VALUES (:id, :uuid, :author, :date, :title, :intro, :text)";
        } else {
            $sql = "UPDATE blog  SET 'uuid' = :uuid, 'author' = :author, 'date' = :date, 'title' = :title,
                    'intro' = :intro, 'text' = :text WHERE id = :id ";
        }

        $dataSql = [];
        $dataSql['id'] = intval($data['id'], 10);
        $dataSql['uuid'] = $data['uuid'];
        $dataSql['author'] = $data['author'];
        $dataSql['date'] = $data['date'];
        $dataSql['title'] = $data['title'];
        $dataSql['intro'] = $data['intro'];
        $dataSql['text'] = $data['text'];
        $dbqObject->query($sql, $dataSql);
    }
}