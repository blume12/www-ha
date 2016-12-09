<?php
/**
 * User: Jasmin
 * Date: 01.12.2016
 */

namespace App\Controller\Backend;


use App\Helper\Session;
use App\Model\BackendUser\BackendUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends BackendController
{
    /**
     * The Action to login a user to the backend.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        $this->setTemplateName('login');
        $this->setPageTitle('Login');

        $this->setRequest($request);

        $backendUser = new BackendUser($this->getConfig());
        $formError = [];
        $formData = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            //$formData = array('title' => 'TEST12');
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = $backendUser->checkErrorsByLogin($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            //$formData['BId'] = $request->attributes->get('id');
            //$backendUser->saveData($formData);

            // TODO: use real hashes!
            $this->setLoggedIn();
            return new RedirectResponse($this->getRoutePath('adminStart'));
        }
        return $this->getResponse(['formData' => $formData, 'errorData' => $formError, 'formAction' => $this->getRoutePath('adminLogin')]);

    }


    /**
     * The action to logout a user to the backend.
     *
     * @return RedirectResponse|Response
     */
    public function logoutAction()
    {
        $this->setLoggedOut();
        Session::removeSession();
        return new RedirectResponse($this->getRoutePath('adminLogin'));
    }
}