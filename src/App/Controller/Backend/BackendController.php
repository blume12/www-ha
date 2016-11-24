<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;


use App\Controller\Controller;
use App\Helper\Session;
use App\Model\BackendUser\BackendUser;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class BackendController extends Controller
{
    /**
     * String to backend path.
     *
     * @var string
     */
    protected $path = '/../../../templates/backend/';


    /**
     * @var null |integer
     */
    private $userId = null;

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
     * Return the user id from the session.
     *
     * @return int|null
     */
    protected function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the Content data with the logged in parameter.
     *
     * @param array $data
     */
    protected function setContentData($data = [])
    {
        parent::setContentData($data);
        $this->contentData = array_merge($this->contentData, array('loggedIn' => $this->loggedIn));
    }

    /**
     * Check if the current user as the permission to be logged in. If the user has no permission anymore,
     * it will redirect to the login screen.
     *
     * @return bool|RedirectResponse
     */
    protected function checkLogin()
    {
        $this->userId = Session::getSessionByKey(BackendUser::getSessionName());
        if ($this->getUserId() == false) {
            $this->setLoggedOut();
            Session::removeSession();
            return new RedirectResponse('/admin');
        }
        $this->setLoggedIn();
        return true;
    }
}