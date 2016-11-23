<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 10:58
 */


use \App\Model\Database\SQLiteConnection;
$config = array();

$sqlLite = new SQLiteConnection();
$config['dbConnection'] = $sqlLite->connect();