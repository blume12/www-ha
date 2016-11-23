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
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Model\Routing\Routing;

date_default_timezone_set("Europe/Berlin");

$response = new Response();

$routes = new RouteCollection();
$routes->add('list', new Route('/', ['_controller' => 'App\Controller\BlogController::listAction']));
$routes->add('programm', new Route('/programm/{id}', ['_controller' => 'App\Controller\BlogController::detailAction'], ['id' => '\d+']));
$routes->add('site-notice', new Route('/impressum', ['_controller' => 'App\Controller\SiteNoticeController::siteNoticeAction']));
$routes->add('welcome', new Route('/hallo', ['_controller' => 'App\Controller\WelcomeController::welcomeAction']));
$routes->add('programm-comment', new Route('/programm/{id}/kommentieren', ['_controller' => 'App\Controller\BlogCommentController::showCommentFormAction'], ['id' => '\d+']));
$routes->add('not-found', new Route('/not-found', ['_controller' => 'App\Controller\NotFoundController::notFoundAction']));
$request = Request::createFromGlobals();
$context = new RequestContext('/');
$context->fromRequest($request);

/* @var $config array */
try {
    $uri = $request->getPathInfo();
    Routing::loadPage($routes, $context, $uri, $request, $config);
} catch (ResourceNotFoundException $e) {
    Routing::loadPage($routes, $context, '/not-found', $request, $config);
}








