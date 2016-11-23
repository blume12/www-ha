<?php

/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 07.11.2016
 * Time: 20:57
 */

namespace App\Model\Database;
/**
 * SQLite connection
 */
class SQLiteConnection
{
    /**
     * PDO instance
     * @var $pdo \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private static $path;

    /**
     * SQLiteConnection constructor.
     */
    public function __construct()
    {
        self::$path = realpath(dirname(__FILE__)) . '/../../../../data/sqlite.db';
    }

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect()
    {

        if ($this->pdo == null) {
            $this->pdo = new \PDO('sqlite:' . self::$path);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->pdo;
    }

}