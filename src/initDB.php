<?php
/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 10.11.2016
 * Time: 19:50
 */


require(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/App/config.php');

use App\Model\Database\SQLiteConnection;
use App\Model\Database\DbQuery;

/* @var $dbConnection PDO */
$db = new SQLiteConnection();
$dbConnection = $db->connect();

/* @var $config array */
$dbqObject = new DbQuery($config);

$sql = "CREATE TABLE IF NOT EXISTS blogComment (BCId INTEGER PRIMARY KEY, BId INTEGER, title VARCHAR(255),
        name VARCHAR(255), text TEXT, createDate TEXT, changeDate TEXT ) ";
$dbqObject->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS blog (id INTEGER PRIMARY KEY, uuid VARCHAR, 'author' VARCHAR(255), 
        'date' TEXT, title VARCHAR(255), intro VARCHAR(255), text TEXT) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS blog_id_uindex ON blog(id) ";
$dbqObject->query($sql);