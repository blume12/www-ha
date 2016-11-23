<?php

/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 10:43
 */
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller
{
    /**
     * Path to the templates.
     *
     * @var string
     */
    private $path = '/../../../templates/';

    /**
     * The template name.
     *
     * @var null|string
     */
    private $templateName = null;
    /**
     * The page title.
     *
     * @var null
     */
    private $pageTitle = null;
    /**
     * The data for the content template.
     *
     * @var array
     */
    private $contentData = array();
    /**
     * @var Twig_Environment $twig
     */
    private $twig;
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var array
     */
    private $config;

    /**
     * Controller constructor. Loads the instance and init twig.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->initTwig();
    }

    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Return the Twig instance.
     *
     * @return Twig_Environment
     */
    protected function getTwig()
    {
        return $this->twig;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected function setRequest($request)
    {
        return $this->request = $request;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $templateName
     */
    protected function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * @param $pageTitle
     */
    protected function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Load the twig module.
     */
    private function initTwig()
    {
        $loader = new Twig_Loader_Filesystem(realpath(dirname(__FILE__)) . $this->path);
        $this->twig = new Twig_Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);
    }

    /**
     * Render the template and return the html.
     *
     * @return string
     */
    private function renderTemplate()
    {
        $html = $this->getTwig()->render($this->templateName . '.twig', $this->contentData);

        return $html;
    }

    /**
     * Set the content data and return the response object.
     *
     * @param array $data
     * @return Response
     */
    protected function getResponse($data = array())
    {
        $this->contentData = array_merge(['pageTitle' => $this->pageTitle], $data);
        $html = $this->renderTemplate();

        return new Response($html);
    }
}