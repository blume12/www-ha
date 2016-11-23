<?php
/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 11:32
 */

namespace App\Controller\Frontend;

use \Symfony\Component\HttpFoundation\Response;

class SiteNoticeController extends FrontendController
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