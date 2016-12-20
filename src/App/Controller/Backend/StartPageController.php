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
use App\Model\Program\ProgramPrice;
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
        $programData = $program->loadData();

        $programPrice = new ProgramPrice($this->getConfig());
        $programPriceData = $programPrice->loadData();

        // TODO: load really values for the statistics

        $countsPrices = [];
        foreach ($programPriceData as $key => $data) {
            $countsPrices['booking'][$key]['count'] = 123;
            $countsPrices['booking'][$key]['name'] = $data['name'];
            $countsPrices['reservation'][$key]['count'] = 124;
            $countsPrices['reservation'][$key]['name'] = $data['name'];
        }

        return $this->getResponse([
            'user' => $userData,
            'countProgram' => count($programData),
            'countMaxPlaces' => count($programData) * 54,
            'countFreePlaces' => 1233,
            'countReservation' => 123,
            'countBooking' => 6,
            'countsPrices' => $countsPrices
        ]);
    }
}