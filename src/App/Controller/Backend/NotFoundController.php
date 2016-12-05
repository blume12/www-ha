<?php
/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:32
 */

namespace App\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Response;

class NotFoundController extends BackendController
{

    /**
     * Load the not Found Action.
     *
     * @return Response
     */
    public function notFoundAction()
    {

        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setTemplateName('404');
        $this->setPageTitle('Nichts gefunden');

        return $this->getResponse();
    }
}