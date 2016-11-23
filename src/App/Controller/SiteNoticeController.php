<?php
/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:32
 */

namespace App\Controller;

use \Symfony\Component\HttpFoundation\Response;

class SiteNoticeController extends Controller
{
    /**
     * Loads the site-notice action.
     *
     * @return Response
     */
    public function siteNoticeAction()
    {
        $this->setTemplateName('site-notice');
        $this->setPageTitle('Impressum');

        return $this->getResponse();
    }
}