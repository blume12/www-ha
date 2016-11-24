<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;


use App\Controller\Controller;

abstract class BackendController extends Controller
{
    /**
     * String to backend path.
     *
     * @var string
     */
    protected $path = '/../../../templates/backend/';

    /**
     * @var bool
     */
    protected $loggedIn = false;

    /**
     * Set the variable for logged in to true.
     */
    protected function setLoggedIn()
    {
        $this->loggedIn = true;
    }

    /**
     * Set the variable for logged in to false.
     */
    protected function setLoggedOut()
    {
        $this->loggedIn = false;
    }

    /**
     * Set the Content data with the logged in parameter.
     *
     * @param array $data
     */
    protected function setContentData($data = [])
    {
        parent::setContentData($data);
        $this->contentData = array_merge($data, array('loggedIn' => $this->loggedIn));
    }
}