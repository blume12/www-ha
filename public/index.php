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

// Todo: FileUpload
date_default_timezone_set("Europe/Berlin");
ini_set('post_max_size', '6M');
ini_set('upload_max_filesize ', '6M');
//ini_set('upload_tmp_dir', '/tmp');
if(!is_dir(dirname(__FILE__).'/../tmp')) {
    mkdir(dirname(__FILE__).'/../tmp');

}

$response = new Response();
$routes = $config['routes'];

$request = Request::createFromGlobals();
$context = new RequestContext('/');
$context->fromRequest($request);

try {
    $uri = $request->getPathInfo();
    Routing::loadPage($routes, $context, $uri, $request, $config);
} catch (ResourceNotFoundException $e) {
    $uriTest = $request->getPathInfo();
    if (preg_match('/admin/', $uriTest)) { // TODO: Check really by the string "admin"?
        $uri = 'adminNotFound';
    } else {
        $uri = 'notFound';
    }
    $uri = Routing::getRoutePath($routes, $uri, $uri);
    Routing::loadPage($routes, $context, $uri, $request, $config);
}