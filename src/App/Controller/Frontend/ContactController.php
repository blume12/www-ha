<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 20:51
 */

namespace App\Controller\Frontend;


use App\Helper\Helper;
use App\Model\Program\Program;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends FrontendController
{
    /**
     * Loads the contact action for the frontend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('contact');
        $this->setPageTitle('Kontakt');

        return $this->getResponse();
    }
}