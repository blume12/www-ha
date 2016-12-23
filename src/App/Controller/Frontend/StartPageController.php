<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:00
 */

namespace App\Controller\Frontend;


use Symfony\Component\HttpFoundation\Response;

class StartPageController extends FrontendController
{

    /**
     * The index action to the start page of the frontend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('start-page');
        $this->setPageTitle('Tickets');

        return $this->getResponse(['programLink' => $this->getRoutePath('programs')]);
    }
}