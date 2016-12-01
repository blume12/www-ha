<?php

/**
 * User: Jasmin
 * Date: 01.12.2016
 */
namespace App\Controller\Documentation;

use App\Controller\Frontend\FrontendController;
use Symfony\Component\HttpFoundation\Response;

class DocumentationController extends FrontendController
{

    /**
     * String to backend path.
     *
     * @var string
     */
    protected $path = '/../../../templates/';

    /**
     * Return the content for the documentation list.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('documentation/documentation');
        $this->setPageTitle('Dokumentation');

        return $this->getResponse();

    }

}