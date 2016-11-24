<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;

use App\Model\BackendUser\BackendUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class StartPageController extends BackendController
{
    /**
     * The index action for the start page of the backend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $user = new BackendUser($this->getConfig());
        $userData = $user->getUserById($this->getUserId());

        $this->setTemplateName('start-page');
        $this->setPageTitle('Übersicht');

        return $this->getResponse(['user' => $userData]);
    }
}