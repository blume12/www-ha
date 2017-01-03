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

        return $this->getResponse([
            'user' => $userData,
            'countProgram' => $program->getCountOfPrograms(),
            'countMaxPlaces' => $program->getCountOfAllPlaces(),
            'countFreePlaces' => 1233,
            'countReservation' => 123,
            'countsPrices' => $countsPrices,
            'reservationSearchAction' => $this->getRoutePath('adminReservationList'),
            'countNotVisiblePrograms' => $program->getCountOfNotVisiblePrograms()
        ]);
    }
}