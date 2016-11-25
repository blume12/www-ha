<?php

/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 13:11
 */
namespace App\Controller\Backend;

use App\Model\BackendUser\BackendUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends BackendController
{
    /**
     * The index Action to login a user to the backend.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $this->setTemplateName('login');
        $this->setPageTitle('Login');

        $this->setRequest($request);

        $this->setPageTitle('Login');

        $backendUser = new BackendUser($this->getConfig());
        $formError = [];
        $formData = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            //$formData = array('title' => 'TEST12');
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $backendUser->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            //$formData['BId'] = $request->attributes->get('id');
            //$backendUser->saveData($formData);

            // TODO: use real hashes!
            $this->setLoggedIn();
            return new RedirectResponse('/admin/start');
        }
        return $this->getResponse(['formData' => $formData, 'formError' => $formError]);

    }
}