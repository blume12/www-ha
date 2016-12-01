<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 10:58
 */


use App\Model\Database\SQLiteConnection;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$config = array();

$sqlLite = new SQLiteConnection();
$config['dbConnection'] = $sqlLite->connect();

// TODO: mabye refactor the routes to arrays/classes
//NOTE: Do NOT use the directly route in the program. Only the key name should be use.
$routes = new RouteCollection();
$routes->add('startPage', new Route('/', ['_controller' => 'App\Controller\Frontend\StartPageController::indexAction']));
$routes->add('programs', new Route('/programme', ['_controller' => 'App\Controller\Frontend\BlogController::listAction']));
$routes->add('programDetail', new Route('/programm/{id}', ['_controller' => 'App\Controller\Frontend\BlogController::detailAction'], ['id' => '\d+']));
$routes->add('siteNotice', new Route('/impressum', ['_controller' => 'App\Controller\Frontend\SiteNoticeController::siteNoticeAction']));
$routes->add('welcome', new Route('/hallo', ['_controller' => 'App\Controller\Frontend\WelcomeController::welcomeAction']));
$routes->add('programm-comment', new Route('/programm/{id}/kommentieren', ['_controller' => 'App\Controller\Frontend\BlogCommentController::showCommentFormAction'], ['id' => '\d+']));
$routes->add('not-found', new Route('/not-found', ['_controller' => 'App\Controller\Frontend\NotFoundController::notFoundAction']));


// admin Routes:
$routes->add('adminLogin', new Route('/admin', ['_controller' => 'App\Controller\Backend\LoginController::indexAction']));
//$routes->add('admin-programme', new Route('/admin/programme/{hash}', ['_controller' => 'App\Controller\Backend\StartPageController::indexAction'], ['hash' => '\w+']));
$routes->add('adminStart', new Route('/admin/start', ['_controller' => 'App\Controller\Backend\StartPageController::indexAction']));
$routes->add('adminProgramList', new Route('/admin/programme', ['_controller' => 'App\Controller\Backend\ProgramController::indexAction']));
$routes->add('adminProgramEdit', new Route('/admin/programm-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\ProgramController::editAction'], ['id' => '\d+']));
$routes->add('adminProgramDelete', new Route('/admin/programm-loeschen/{id}', ['_controller' => 'App\Controller\Backend\ProgramController::deleteAction'], ['id' => '\d+']));
$routes->add('adminTimescaleList', new Route('/admin/zeitraum', ['_controller' => 'App\Controller\Backend\TimescaleController::indexAction']));
$routes->add('adminTextSourceList', new Route('/admin/textvorlagen', ['_controller' => 'App\Controller\Backend\TextSourceController::indexAction']));
$routes->add('adminBackendUserList', new Route('/admin/nutzer', ['_controller' => 'App\Controller\Backend\BackendUserController::indexAction']));
$routes->add('adminLogout', new Route('/admin/logout', ['_controller' => 'App\Controller\Backend\LogoutController::indexAction'], ['hash' => '\w+']));

// documenation Routes:
$routes->add('documentation', new Route('/dokumentation', ['_controller' => 'App\Controller\Documentation\DocumentationController::indexAction']));

$config['routes'] = $routes;