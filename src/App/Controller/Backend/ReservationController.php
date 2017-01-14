<?php
/**
 * Author: Jasmin Stern
 * Date: 02.01.2017
 * Time: 19:51
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
use App\Helper\StandardStock;
use App\Helper\Validator;
use App\Model\Program\Program;
use App\Model\Program\ProgramPrice;
use App\Model\Reservation\Reservation;
use App\Model\ShoppingCart\ShoppingCart;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends BackendController
{

    private static $mainTemplateName = 'reservation';
    private static $mainPageTitle = 'Reservierung';
    private static $routeNameList = 'adminReservationList';
    private static $routeNameDelete = 'adminReservationDelete';
    private static $routeNameEdit = 'adminReservationEdit';

    /**
     * Search for reservation by name, email or reservation number.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function searchAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $this->setTemplateName('reservation-list');
        $this->setPageTitle('Reservierungen');

        $searchValue = $this->getRequest()->request->get('reservationName');

        $reservation = new Reservation($this->getConfig());
        $reservationData = $reservation->searchData($searchValue);

        if (count($reservationData) == 1) {
            return new RedirectResponse($this->getRoutePath('adminReservationEdit', ['id' => $reservationData[0]['RId']]));
        }

        foreach ($reservationData as $key => $data) {
            $reservationData[$key]['editRoute'] = $this->getRoutePath('adminReservationEdit', ['id' => $data['RId']]);
            $reservationData[$key]['deleteRoute'] = $this->getRoutePath('adminReservationDelete', ['id' => $data['RId']]);
        }
        return $this->getResponse([
            'reservationData' => $reservationData,
            'reservationName' => $searchValue,
            'showSearchInput' => true
        ]);
    }

    /**
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function listPerProgramAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $this->setTemplateName('reservation-list');
        $this->setPageTitle('Reservierungen pro Program');

        $pid = $this->getRequest()->attributes->get('id');
        $reservation = new Reservation($this->getConfig());
        $reservationData = $reservation->loadSpecificEntryPerProgram($pid);

        $program = new Program($this->getConfig());
        $programData = $program->loadSpecificEntry($pid);
        foreach ($reservationData as $key => $data) {
            $reservationData[$key]['editRoute'] = $this->getRoutePath('adminReservationEdit', ['id' => $data['RId']]);
            $reservationData[$key]['deleteRoute'] = $this->getRoutePath('adminReservationDelete', ['id' => $data['RId']]);
        }
        return $this->getResponse([
            'reservationData' => $reservationData,
            'showSearchInput' => false,
            'countReservation' => $reservation->getCountReservationByProgram($pid),
            'countMax' => Program::getMaxReservationPerProgram(),
            'programData' => $programData,
            'newReservationRoute' => $this->getRoutePath('adminReservationNewByProgram', ['id' => $pid])
        ]);
    }


    /**
     * Loads the action for the reservation edit form.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function editAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName(self::$mainTemplateName . '-edit');
        $this->setPageTitle('Reservierung bearbeiten');

        $reservation = new Reservation($this->getConfig());
        $shoppingCart = new ShoppingCart($this->getConfig());
        $programData = $shoppingCart->loadShoppingCartDataFromDB($id);


        $formData = $reservation->loadSpecificEntry($id);
        $statusData = Reservation::getStatusArray();
        if ($formData['status'] == 'expired') {
            unset($statusData['open']);
        }


        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            if ($formData == null) {
                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = array_merge($formData, $this->getRequest()->request->all());
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $reservation->saveDataInBackend($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameEdit, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
            'programData' => $programData,
            'statusData' => $statusData
        ]);
    }

    /**
     * Loads the action for the new reservation by program form.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $this->setTemplateName('reservation-program-new');
        $this->setPageTitle('Reservierung neu anlegen');

        $this->setRequest($request);
        $programId = $this->getRequest()->attributes->get('id');

        $program = new Program($this->getConfig());
        $programData = $program->loadSpecificEntry($programId);
        $programPrice = new ProgramPrice($this->getConfig());
        $programPriceData = $programPrice->loadSpecificEntry($programData['price']);
        $reservation = new Reservation($this->getConfig());
        $countOfTickets = StandardStock::getCountOfTickets($reservation->getCountToReserveActually($programId));
        $formError = [];
        $formSuccess = [];
        $formData = [];

        $maxReservation = $reservation->getCountToReserveActually($programId);

        if ($request->getMethod() !== 'POST') {
            // Set default values
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $countNormal = $formData['countTickets'];
            $countSale = $formData['countSaleTickets'];
            if ($countNormal <= 0 && $countSale <= 0) {
                $formError['countTickets'] = 'Bitte geben Sie die Anzahl der Tickets ein.';
                $formError['countSaleTickets'] = 'Bitte geben Sie die Anzahl der Tickets ein.';
            }
            if ($countNormal + $countSale > $maxReservation) {
                $formError['count_' . $programId] = "Es sind nicht mehr genügend Tickets online. Maximal " . $maxReservation . ' Ticket' . ($maxReservation == 1 ? '' : 's');
            }
            if (!isset($formData['status'])) {
                $formError['status'] = 'Bitte geben Sie einen Status ein.';
            }
            if (!Validator::isAlpha($formData['firstname'])) {
                $formError['firstname'] = 'Bitte geben Sie einen Vornamen ein.';
            }
            if (!Validator::isAlpha($formData['lastname'])) {
                $formError['lastname'] = 'Bitte geben Sie einen Nachnamen ein.';
            }
            if (!Validator::isEmail($formData['email'])) {
                $formError['email'] = 'Bitte geben Sie eine E-Mail-Adresse ein.';
            }
        }
        $ticketsForSale = $maxReservation > 0;

        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */

            $formData['program'] = [
                [
                    'PId' => $programId,
                    'priceMode' => 1,
                    'countTickets' => $formData['countTickets'],
                    'price' => $programPriceData['price']
                ],
                [
                    'PId' => $programId,
                    'priceMode' => 2,
                    'countTickets' => $formData['countSaleTickets'],
                    'price' => $programPriceData['priceReduce']
                ]
            ];
            $reservation->saveData($formData, true);
            return new RedirectResponse($this->getRoutePath('adminReservationListPerProgram', ['id' => $programId]));
        }

        $statusData = Reservation::getStatusArray();
        unset($statusData['open']);
        unset($statusData['expired']);

        return $this->getResponse([
            'formAction' => $this->getRoutePath('adminReservationNewByProgram', ['id' => $programId]),
            'programData' => $programData,
            'countsNormal' => $countOfTickets,
            'countsSale' => $countOfTickets,
            'formData' => $formData,
            'successData' => $formSuccess,
            'errorData' => $formError,
            'ticketsForSale' => $ticketsForSale,
            'statusData' => $statusData,
            'programPriceData' => $programPriceData
        ]);
    }

    /**
     * The action to delete a reservation.
     *
     * @param Request $request
     * @return bool|RedirectResponse|Response
     */
    public function deleteAction(Request $request)
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }
        $this->setRequest($request);
        $id = $this->getRequest()->attributes->get('id');

        $this->setTemplateName(self::$mainTemplateName . '-delete');
        $this->setPageTitle(self::$mainPageTitle . ' löschen');

        $reservation = new Reservation($this->getConfig());

        $formError = [];
        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData = $reservation->loadSpecificEntry($id);
            if ($formData == null) {

                return new RedirectResponse($this->getRoutePath('adminNotFound'));
            }
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $formError = Helper::checkErrorForDelete($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $reservation->deleteData($id);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameDelete, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
        ]);
    }

}