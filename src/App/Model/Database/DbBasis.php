<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 11:18
 */

namespace App\Model\Database;


abstract class DbBasis
{
    /**
     * @var null | DbQuery
     */
    private $dbqObject = null;
    /**
     * @var array
     */
    protected $config;


    /**
     * DbBasis constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Return the DbQuery object. It will only create a new object, if it's null.
     *
     * @return DbQuery|null
     */
    protected function getDbqObject()
    {
        if ($this->dbqObject == null) {
            $this->dbqObject = new DbQuery($this->config);
        }
        return $this->dbqObject;
    }

    /**
     * Return the config data array.
     *
     * @return array
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Return if it is frontend.
     *
     * @return bool
     */
    protected function isFrontend()
    {
        return $this->getConfig()['frontend'];
    }

    /**
     * Get main url from config data array.
     *
     * @return mixed
     */
    protected function getMainUrl()
    {
        return $this->getConfig()['url'];
    }

}