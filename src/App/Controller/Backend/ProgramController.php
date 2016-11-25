<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends BackendController
{

    /**
     * Loads the action for the program list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName('program-list');
        $this->setPageTitle('Programme');

        return $this->getResponse();
    }

}