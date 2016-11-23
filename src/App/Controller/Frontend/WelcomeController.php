<?php
/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:32
 */

namespace App\Controller\Frontend;

use App\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class WelcomeController extends FrontendController
{
    /**
     * Loads the welcome Action.
     *
     * @param Request $request
     * @return Response
     */
    public function welcomeAction(Request $request)
    {
        $name = Helper::escapeString($request->request->get('name'));
        if ($name == '') {
            $name = 'Welt';
        }
        $this->setTemplateName('welcome');
        $this->setPageTitle('Hallo ' . $name);

        return $this->getResponse(['welcomeName' => $name]);
    }

}