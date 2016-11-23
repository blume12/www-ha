<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;

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
        $this->setTemplateName('start-page');
        $this->setPageTitle('Übersicht');

        return $this->getResponse();
    }
}