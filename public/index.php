<?php
/**
 * User: Jasmin Stern (stja7017)
 * Date: 06.10.16
 * Time: 10:16
 */

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

require_once(dirname(__FILE__) . '/../src/App/config.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Helper\Routing\Routing;
use App\Helper\Session;

Session::startSession();

/* @var $config array */

date_default_timezone_set("Europe/Berlin");

$response = new Response();
$routes = $config['routes'];

$request = Request::createFromGlobals();
$context = new RequestContext('/');
$context->fromRequest($request);

try {
    $uri = $request->getPathInfo();
    Routing::loadPage($routes, $context, $uri, $request, $config);
} catch (ResourceNotFoundException $e) {
    Routing::loadPage($routes, $context, '/not-found', $request, $config);
}








