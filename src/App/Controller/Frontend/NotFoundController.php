<?php
/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:32
 */

namespace App\Controller\Frontend;

use \Symfony\Component\HttpFoundation\Response;

class NotFoundController extends FrontendController
{

    /**
     * Load the not Found Action.
     *
     * @return Response
     */
    public function notFoundAction()
    {
        $this->setTemplateName('404');
        $this->setPageTitle('Nichts gefunden');

        return $this->getResponse();
    }
}