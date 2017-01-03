<?php
/**
 * Author: Jasmin Stern
 * Date: 14.11.2016
 * Time: 21:21
 */

namespace App\Model\Database;


class DbQuery
{
    /**
     * @var null|\PDO
     */
    protected $dbConnection = null;

    /**
     * @var $sqlStatement \PDOStatement ;
     */
    private $sqlStatement;

    /**
     * DbModel constructor.
     * Loads the db-connection.
     * @param $config
     */
    public function __construct($config)
    {
        if ($this->dbConnection == null) {
            $this->dbConnection = $config['dbConnection'];
        }
    }

    /**
     * It will run the query for sqlite.
     *
     * @param $query
     * @param array $data
     * @return bool
     */
    public function query($query, $data = [])
    {
        $result = false;
        $this->sqlStatement = null;

        try {
            $this->sqlStatement = $this->dbConnection->prepare($query);
            if (count($data) > 0) {

                foreach ($data as $key => $value) {
                    $this->sqlStatement->bindValue($key, $value);
                }
            }
            $result = $this->sqlStatement->execute();
        } catch (\Exception $exception) {
            echo $exception->getMessage() . " \n";
        }
        return $result;
    }

    /**
     * Return the next row of the database query.
     *
     * @return mixed
     */
    public function nextRow()
    {
        return $this->sqlStatement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Return the number of the database query.
     *
     * @return int
     */
    public function numberOfRows()
    {
        return count($this->sqlStatement->fetchAll(\PDO::FETCH_ASSOC));
    }

}