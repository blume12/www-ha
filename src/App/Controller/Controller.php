<?php

/**
 * Created by PhpStorm.
 * User: stja7017
 * Date: 27.10.16
 * Time: 10:43
 */
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
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
    protected $path = '/../../../templates/';

    /**
     * @var array
     */
    protected $menuArray = [];

    /**
     * @var string
     */
    protected $notFoundRoute = 'notFound';

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
    protected $contentData = array();
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

    /**
     * Return the config data array.
     *
     * @return array
     */
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
        $html = $this->getTwig()->render($this->templateName . '.html.twig', $this->contentData);

        return $html;
    }

    /**
     * Set the content data and return the response object.
     *
     * @param array $data
     * @return Response
     */
    protected function getResponse($data = [])
    {
        $this->setContentData($data);
        $html = $this->renderTemplate();

        return new Response($html);
    }

    /**
     * Set the content data for the request.
     *
     * @param array $data
     */
    protected function setContentData($data = [])
    {
        $this->contentData = array_merge(['pageTitle' => $this->pageTitle, 'menuData' => $this->getMenu()], $data);
    }


    /**
     * Return the menu data array.
     *
     * @return array
     */
    private function getMenu()
    {
        return $this->menuArray;
    }


    /**
     * @return RouteCollection
     */
    private function getRouteCollection()
    {
        return $this->getConfig()['routes'];
    }

    /**
     * @param $routeName
     * @param array $parameterData
     * @return string
     */
    protected function getRoutePath($routeName, $parameterData = [])
    {
        $path = $this->getRouteCollection()->get($routeName)->getPath();
        $parameters = $this->getRouteCollection()->get($routeName)->getRequirements();
        foreach ($parameters as $name => $regex) {
            if (!preg_match('/' . str_replace('\\', '', $regex) . '/', $parameters[$name])) {
                return $this->getRouteCollection()->get($this->notFoundRoute)->getPath();
            }
            if (isset($parameterData[$name])) {
                $path = str_replace('{' . $name . '}', $parameterData[$name], $path);
            } else {
                // the path is wrong. so route to the notFound page.
                return $this->getRouteCollection()->get($this->notFoundRoute)->getPath();
            }
        }
        return $path;
    }
}