<?php
/**
 * Author: Jasmin Stern
 * Date: 02.01.2017
 * Time: 19:51
 */

namespace App\Controller\Backend;


use App\Helper\Helper;
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
            'reservationName' => $searchValue
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
            $formError = $reservation->checkErrors($formData);
        }
        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $formData['id'] = $id;
            $reservation->saveData($formData);

            return new RedirectResponse($this->getRoutePath(self::$routeNameList));
        }


        return $this->getResponse([
            'formAction' => $this->getRoutePath(self::$routeNameEdit, ['id' => $id]),
            'formData' => $formData,
            'errorData' => $formError,
            'programData' => $programData,
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