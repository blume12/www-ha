<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BackendUserController extends BackendController
{
    /**
     * Loads the action for the backend user list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName('backend-user-list');
        $this->setPageTitle('Nutzer');

        return $this->getResponse();
    }

}