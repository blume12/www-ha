<?php
/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 07.11.2016
 * Time: 14:09
 */

namespace App\Model\Blog;


use App\Helper\Validator;
use App\Model\Database\DbBasis;

class BlogComment extends DbBasis
{
    /**
     * The id of a blog.
     *
     * @var int|null
     */
    public $blogId = null;

    /**
     * BlogComment constructor.
     * @param $config
     * @param $blogId
     */
    public function __construct($config, $blogId)
    {
        parent::__construct($config);
        $this->blogId = intval($blogId, 10);
    }

    /**
     * Return the comments of a specific blogId.
     *
     * @return array
     */
    public function getComment()
    {
        $dbqObject = $this->getDbqObject();

        $data = [];
        $sqlData = ['BId' => $this->blogId];
        $query = "SELECT text, title, BId, name,createDate, changeDate FROM blogComment WHERE BId=:BId ";

        $dbqObject->query($query, $sqlData);

        $i = 0;
        while ($row = $dbqObject->nextRow()) {
            $data[] = $row;
            $data[$i]['createDate'] = date('d.m.Y H:i', strtotime($row['createDate']));
            $i++;
        }

        return $data;
    }

    /**
     * Save the data of a commment for a specific blog.
     *
     * @param $data
     */
    public function saveData($data)
    {
        $dbqObject = $this->getDbqObject();
        $currentDate = new \DateTime();
        $createDate = $currentDate->format('Y-m-d H:i:s');
        $sqlData = [
            'BId' => $data['BId'],
            'title' => $data['title'],
            'text' => $data['text'],
            'name' => $data['name'],
            'createDate' => $createDate,
            'changeDate' => $createDate
        ];

        $insert = "INSERT INTO blogComment ( 'title', 'text','BId','name','createDate','changeDate') 
                   VALUES (:title, :text, :BId, :name, :createDate, :changeDate)";
        $dbqObject->query($insert, $sqlData);
    }

    /**
     * Retrun a array with the errors of the formData.
     *
     * @param $formData
     * @return array
     */
    public function checkErrors($formData)
    {
        $formError = [];

        if (!Validator::isAlpha($formData['name'], true)) {
            $formError['name'] = 'Bitte geben Sie einen Namen an.';
        }
        if (!Validator::isAlpha($formData['title'], true)) {
            $formError['title'] = 'Bitte geben Sie einen Titel an.';
        }
        if (!Validator::isAlpha($formData['text'], true)) {
            $formError['text'] = 'Bitte geben Sie einen Text an.';
        }
        return $formError;
    }
}