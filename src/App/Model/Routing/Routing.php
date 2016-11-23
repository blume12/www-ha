<?php

/**
 * Created by PhpStorm.
 * User: Jasmin
 * Date: 07.11.2016
 * Time: 13:44
 */
namespace App\Model\Routing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;

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
}