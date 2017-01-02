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
$routes->add('programs', new Route('/programme', ['_controller' => 'App\Controller\Frontend\ProgramController::indexAction']));
$routes->add('programDetail', new Route('/programm/{id}', ['_controller' => 'App\Controller\Frontend\ProgramController::detailAction'], ['id' => '\d+']));
$routes->add('siteNotice', new Route('/impressum', ['_controller' => 'App\Controller\Frontend\SiteNoticeController::siteNoticeAction']));
$routes->add('contact', new Route('/kontakt', ['_controller' => 'App\Controller\Frontend\ContactController::indexAction']));
$routes->add('shoppingCartList', new Route('/warenkorb', ['_controller' => 'App\Controller\Frontend\ShoppingCartController::overviewAction']));
$routes->add('shoppingCartDelete', new Route('/warenkorb-loeschen/{id}-{priceMode}', ['_controller' => 'App\Controller\Frontend\ShoppingCartController::deleteAction'], ['id' => '\d+', 'priceMode' => '\d+']));
$routes->add('notFound', new Route('/not-found', ['_controller' => 'App\Controller\Frontend\NotFoundController::notFoundAction']));


// admin Routes:
$routes->add('adminLogin', new Route('/admin', ['_controller' => 'App\Controller\Backend\SessionController::loginAction']));
$routes->add('adminLogout', new Route('/admin/logout', ['_controller' => 'App\Controller\Backend\SessionController::logoutAction']));
$routes->add('adminStart', new Route('/admin/start', ['_controller' => 'App\Controller\Backend\StartPageController::indexAction']));
//Programs:
$routes->add('adminProgramList', new Route('/admin/programme', ['_controller' => 'App\Controller\Backend\ProgramController::indexAction']));
$routes->add('adminProgramNew', new Route('/admin/programm-anlegen', ['_controller' => 'App\Controller\Backend\ProgramController::newEntryAction']));
$routes->add('adminProgramEdit', new Route('/admin/programm-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\ProgramController::editAction'], ['id' => '\d+']));
$routes->add('adminProgramDelete', new Route('/admin/programm-loeschen/{id}', ['_controller' => 'App\Controller\Backend\ProgramController::deleteAction'], ['id' => '\d+']));
// reservation:
$routes->add('adminReservationList', new Route('/admin/reservierungen', ['_controller' => 'App\Controller\Backend\ReservationController::searchAction']));
$routes->add('adminReservationEdit', new Route('/admin/reservierung-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\ReservationController::editAction'], ['id' => '\d+']));
$routes->add('adminReservationDelete', new Route('/admin/reservierung-loeschen/{id}', ['_controller' => 'App\Controller\Backend\ReservationController::deleteAction'], ['id' => '\d+']));
// Programs prices:
$routes->add('adminProgramPriceList', new Route('/admin/programm-preise', ['_controller' => 'App\Controller\Backend\ProgramPriceController::indexAction']));
$routes->add('adminProgramPriceNew', new Route('/admin/programm-preis-anlegen', ['_controller' => 'App\Controller\Backend\ProgramPriceController::newEntryAction']));
$routes->add('adminProgramPriceEdit', new Route('/admin/programm-preis-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\ProgramPriceController::editAction'], ['id' => '\d+']));
$routes->add('adminProgramPriceDelete', new Route('/admin/programm-preis-loeschen/{id}', ['_controller' => 'App\Controller\Backend\ProgramPriceController::deleteAction'], ['id' => '\d+']));
//Timescale:
$routes->add('adminTimescaleList', new Route('/admin/zeitraum', ['_controller' => 'App\Controller\Backend\TimescaleController::indexAction']));
$routes->add('adminTimescaleNew', new Route('/admin/zeitraum-anlegen', ['_controller' => 'App\Controller\Backend\TimescaleController::newEntryAction']));
$routes->add('adminTimescaleEdit', new Route('/admin/zeitraum-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\TimescaleController::editAction'], ['id' => '\d+']));
$routes->add('adminTimescaleDelete', new Route('/admin/zeitraum-loeschen/{id}', ['_controller' => 'App\Controller\Backend\TimescaleController::deleteAction'], ['id' => '\d+']));
//TextSource:
$routes->add('adminTextSourceList', new Route('/admin/textvorlagen', ['_controller' => 'App\Controller\Backend\TextSourceController::indexAction']));
$routes->add('adminTextSourceNew', new Route('/admin/textvorlage-anlegen', ['_controller' => 'App\Controller\Backend\TextSourceController::newEntryAction']));
$routes->add('adminTextSourceEdit', new Route('/admin/textvorlage-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\TextSourceController::editAction'], ['id' => '\d+']));
$routes->add('adminTextSourceDelete', new Route('/admin/textvorlage-loeschen/{id}', ['_controller' => 'App\Controller\Backend\TextSourceController::deleteAction'], ['id' => '\d+']));
//BackendUser:
$routes->add('adminBackendUserList', new Route('/admin/nutzer', ['_controller' => 'App\Controller\Backend\BackendUserController::indexAction']));
$routes->add('adminBackendUserNew', new Route('/admin/nutzer-anlegen', ['_controller' => 'App\Controller\Backend\BackendUserController::newEntryAction']));
$routes->add('adminBackendUserEdit', new Route('/admin/nutzer-bearbeiten/{id}', ['_controller' => 'App\Controller\Backend\BackendUserController::editAction'], ['id' => '\d+']));
$routes->add('adminBackendUserDelete', new Route('/admin/nutzer-loeschen/{id}', ['_controller' => 'App\Controller\Backend\BackendUserController::deleteAction'], ['id' => '\d+']));
//not found:
$routes->add('adminNotFound', new Route('/admin/not-found', ['_controller' => 'App\Controller\Backend\NotFoundController::notFoundAction']));

// documenation Routes:
$routes->add('documentation', new Route('/dokumentation', ['_controller' => 'App\Controller\Documentation\DocumentationController::indexAction']));

$config['routes'] = $routes;
$config['post'] = $_POST;