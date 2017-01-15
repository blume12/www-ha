<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 20:56
 */

namespace App\Controller\Backend;

use App\Helper\StandardStock;
use App\Model\BackendUser\BackendUser;
use App\Model\Program\Program;
use App\Model\Reservation\Reservation;
use App\Model\TextSource\TextSource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class StartPageController extends BackendController
{
    /**
     * The index action for the start page of the backend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $login = $this->checkLogin();
        if ($login instanceof RedirectResponse) {
            return $login;
        }

        $jsFiles = ['/js/backend/statistic.js'];

        $user = new BackendUser($this->getConfig());
        $userData = $user->getUserById($this->getUserId());
        $userData['appellation'] = StandardStock::getAppellation($userData['appellation']);

        $this->setTemplateName('start-page');
        $this->setPageTitle('Übersicht');

        $program = new Program($this->getConfig());
        $program->loadData(true);

        // TODO: load really values for the statistics

        $countsPrices = [];
        $countsPrices['reservation'][1]['count'] = 124;
        $countsPrices['reservation'][1]['name'] = 'normalem Preis';

        $countsPrices['reservation'][2]['count'] = 2;
        $countsPrices['reservation'][2]['name'] = 'reduziertem Preis';

        $reservation = new Reservation($this->getConfig());

        $countMaxPlaces = $program->getCountOfAllPlaces();
        $countReservation = $reservation->getCountReservationByProgram();

        $textSource = new TextSource($this->getConfig());
        $textSourceCount = count($textSource->loadData());

        $errorData = [];
        if ($textSourceCount <= 0) {
            $errorData['hasTextSource'] = 'Es ist keine Textvorlage vorhanden. Bitte legen Sie mindestens eine an.
                                            <a href="' . $this->getRoutePath('adminTextSourceNew') . '">Textvorlage anlegen</a>';
        } else {
            $textSourceCountActive = count($textSource->loadData(true));
            if ($textSourceCountActive <= 0) {
                $errorData['hasTextSource'] = 'Es ist keine Textvorlage als "aktiviert" markiert. Bitte ändern Sie das.
                                                <a href="' . $this->getRoutePath('adminTextSourceList') . '">Textvorlagen</a>';
            }
        }
        return $this->getResponse([
            'user' => $userData,
            'countProgram' => $program->getCountOfPrograms(),
            'countMaxPlaces' => $countMaxPlaces,
            'countFreePlaces' => $countMaxPlaces - $countReservation,
            'countReservation' => $countReservation,
            'countsPrices' => $countsPrices,
            'reservationSearchAction' => $this->getRoutePath('adminReservationList'),
            'countNotVisiblePrograms' => $program->getCountOfNotVisiblePrograms(),
            'countReservationAll' => $reservation->getCountReservationByStatus('all'),
            'countReservationOpen' => $reservation->getCountReservationByStatus('open'),
            'countReservationPaid' => $reservation->getCountReservationByStatus('paid'),
            'countReservationExpired' => $reservation->getCountReservationByStatus('expired'),
            'linkProgram' => $this->getRoutePath('adminProgramList'),
            'javascriptFiles' => $jsFiles,
            'errorData' => $errorData
        ]);
    }
}