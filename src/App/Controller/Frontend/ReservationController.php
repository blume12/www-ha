<?php
/**
 * Author: Jasmin Stern
 * Date: 03.01.2017
 * Time: 21:37
 */

namespace App\Controller\Frontend;


use App\Model\Reservation\Reservation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends FrontendController
{

    /**
     * The confirm action for a reservation.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function confirmAction(Request $request)
    {
        $this->setRequest($request);
        $this->setTemplateName('reservation-confirm');
        $this->setPageTitle('Reservierung Bestätigung');

        $reservation = new Reservation($this->getConfig());
        $reservationNumber = $this->getRequest()->attributes->get('reservationNumber');
        $reservationData = $reservation->loadSpecificEntryByReservationNumber($reservationNumber);
        if (count($reservationData) <= 0) {
            return new RedirectResponse($this->getRoutePath('notFound'));
        } else if ($reservationData['reservationExpired']) {
            $reservation->saveAsExpired($reservationNumber);
            return new RedirectResponse($this->getRoutePath('reservationExpired'));
        }
        $reservation->saveAsConfirm($reservationNumber);
        return $this->getResponse();
    }

    /**
     * The expired action for a reservation.
     *
     * @return Response
     */
    public function expiredAction()
    {
        $this->setTemplateName('reservation-expired');
        $this->setPageTitle('Reservierung abgelaufen');
        return $this->getResponse();
    }
}