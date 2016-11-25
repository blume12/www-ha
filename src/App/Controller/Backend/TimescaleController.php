<?php
/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Controller\Backend;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TimescaleController extends BackendController
{

    /**
     * Loads the action for the timescale list.
     *
     * @return bool|RedirectResponse|Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName('timescale-list');
        $this->setPageTitle('Zeiträume');

        return $this->getResponse();
    }

}