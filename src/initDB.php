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
use App\Model\BackendUser\BackendUser;

/* @var $dbConnection PDO */
$db = new SQLiteConnection();
$dbConnection = $db->connect();

/* @var $config array */
$dbqObject = new DbQuery($config);

$sql = "CREATE TABLE IF NOT EXISTS backendUser (BUId INTEGER PRIMARY KEY, appellation VARCHAR(255), firstname VARCHAR(255),
        lastname VARCHAR(255), username VARCHAR(255), password BLOB, createDate TEXT, changeDate TEXT, loginDate TEXT, privilege TEXT ) ";
$dbqObject->query($sql);
$sql = "CREATE UNIQUE INDEX IF NOT EXISTS backendUser_BUId_uindex ON backendUser(BUId) ";
$dbqObject->query($sql);

$userData = [
    'appellation' => 'mr',
    'firstname' => '',
    'lastname' => 'Master',
    'username' => 'admin',
    'password' => 'admin',
    'privilege' => 'superAdmin'
];

$backendUser = new BackendUser($config);
$backendUser->saveData($userData);


$sql = "CREATE TABLE IF NOT EXISTS program (PId INTEGER PRIMARY KEY, uuid VARCHAR, author VARCHAR(255), 
        'date' TEXT, title VARCHAR(255), intro VARCHAR(255), text TEXT) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS program_id_uindex ON program(PId) ";
$dbqObject->query($sql);

// Program Price:
$sql = "CREATE TABLE IF NOT EXISTS programPrice (PPId INTEGER PRIMARY KEY, name VARCHAR(255), price DECIMAL(4,2), priceReduce DECIMAL(4,2)) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS programPrice_id_uindex ON programPrice(PPId) ";
$dbqObject->query($sql);

// programm + Program Price:
$sql = "CREATE TABLE IF NOT EXISTS program_programPrice (id INTEGER PRIMARY KEY, PId INTEGER, PPId  INTEGER) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS program_programPrice_id_uindex ON program_programPrice(PPPId) ";
$dbqObject->query($sql);

// Text source:
$sql = "CREATE TABLE IF NOT EXISTS textSource (TSId INTEGER PRIMARY KEY, title VARCHAR(255), text TEXT) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS textSource_id_uindex ON textSource(TSId) ";
$dbqObject->query($sql);

// Timescale:
$sql = "CREATE TABLE IF NOT EXISTS timescale (TId INTEGER PRIMARY KEY, year VARCHAR(255), fromDate TEXT, untilDate TEXT) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS timescale_id_uindex ON timescale(TId) ";
$dbqObject->query($sql);

// Reservation:
$sql = "CREATE TABLE IF NOT EXISTS reservation (RId INTEGER PRIMARY KEY, reservationNumber VARCHAR(50), firstname VARCHAR(255), lastname VARCHAR(255), email VARCHAR(255)) ";
$dbqObject->query($sql);

$sql = "CREATE UNIQUE INDEX IF NOT EXISTS reservation_id_uindex ON reservation(RId) ";
$dbqObject->query($sql);