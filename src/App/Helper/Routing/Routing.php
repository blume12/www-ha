<?php

/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 07.11.2016
 * Time: 13:44
 */
namespace App\Helper\Routing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;

class Routing
{
    /**
     * Load the page of the given uri. It will check by the routes.
     *
     * @param $routes
     * @param $context
     * @param $uri
     * @param $request
     * @param $config
     */
    public static function loadPage($routes, $context, $uri, $request, $config)
    {
        $matcher = new UrlMatcher($routes, $context);
        $parameters = $matcher->match($uri);
        foreach ($parameters as $key => $value) {
            $request->attributes->set($key, $value);
        }
        if (!is_null($parameters)) {
            $controllerMap = preg_split('/::/', $parameters['_controller']);
            $controllerClass = $controllerMap[0];
            $action = isset($controllerMap[1]) ? $controllerMap[1] : null;
            if ($action) {
                $controller = new $controllerClass($config);
                $response = $controller->$action($request);
            } else {
                $response = new Response('Server error', 500);
            }
        } else {
            $response = new Response('Not Found', 404);
        }
        $response->send();
    }


    /**
     * Return the route path to a route name from the config array.
     *
     * @param $routeCollection RouteCollection
     * @param $notFoundRoute
     * @param $routeName
     * @param array $parameterData
     * @return mixed
     */
    public static function getRoutePath($routeCollection, $notFoundRoute, $routeName, $parameterData = [])
    {
        $path = $routeCollection->get($routeName)->getPath();
        $parameters = $routeCollection->get($routeName)->getRequirements();
        foreach ($parameters as $name => $regex) {
            if (!preg_match('/' . str_replace('\\', '', $regex) . '/', $parameters[$name])) {
                return $routeCollection->get($notFoundRoute)->getPath();
            }
            if (isset($parameterData[$name])) {
                $path = str_replace('{' . $name . '}', $parameterData[$name], $path);
            } else {
                // the path is wrong. so route to the notFound page.
                return $routeCollection->get($notFoundRoute)->getPath();
            }
        }
        return $path;
    }
}