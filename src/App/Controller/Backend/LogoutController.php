<?php

/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 13:11
 */
namespace App\Controller\Backend;

use App\Helper\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends BackendController
{
    /**
     * The index Action to logout a user to the backend.
     *
     * @return RedirectResponse|Response
     */
    public function indexAction()
    {
        $this->setLoggedOut();
        Session::removeSession();
        return new RedirectResponse($this->getRoutePath('adminLogin'));
    }
}