<?php
/**
 * Author: Jasmin Stern
 * Date: 06.12.2016
 * Time: 20:51
 */

namespace App\Controller\Frontend;


use App\Helper\Helper;
use App\Helper\StandardStock;
use App\Model\Program\Program;
use App\Model\Reservation\Reservation;
use App\Model\ShoppingCart\ShoppingCart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends FrontendController
{
    /**
     * Loads the program action for the frontend.
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->setTemplateName('program-list');
        $this->setPageTitle('Programmblöcke');

        $program = new Program($this->getConfig());
        $programData = $program->loadData();

        foreach ($programData as $key => $value) {
            $programData[$key] = $value;
            $programData[$key]['text'] = Helper::maxWords($value['text'], 50);
            $programData[$key]['detailLink'] = $this->getRoutePath('programDetail', ['id' => $value['PId']]);
        }

        return $this->getResponse([
            'programData' => $programData
        ]);
    }

    /**
     * Loads the program detail action for the frontend.
     *
     * @param Request $request
     * @return Response
     */
    public function detailAction(Request $request)
    {
        $this->setRequest($request);
        $programId = $this->getRequest()->attributes->get('id');

        $this->setTemplateName('program-detail');
        $this->setPageTitle('Programmblock');

        $program = new Program($this->getConfig());
        $programData = $program->loadSpecificEntry($programId);
        $reservation = new Reservation($this->getConfig());

        $maxReservation = $reservation->getCountToReserveActually($programId, $programData['countTickets']);
        $countOfTickets = StandardStock::getCountOfTickets($maxReservation);
        $shoppingCart = new ShoppingCart($this->getConfig());
        $formError = [];
        $formSuccess = [];
        $formData = [];


        if ($request->getMethod() !== 'POST') {
            // Set default values
            $formData['countTickets'] = $shoppingCart->loadShoppingCartDataPerProgram($programId, 1);
            $formData['countSaleTickets'] = $shoppingCart->loadShoppingCartDataPerProgram($programId, 2);
        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();
            $countNormal = $formData['countTickets'];
            $countSale = $formData['countSaleTickets'];
            if ($countNormal + $countSale > $maxReservation) {
                $formError['count_' . $programId] = "Es sind nicht mehr genügend Tickets online. Maximal ";
                $formError['count_' . $programId] .= $maxReservation . ' Ticket' . ($maxReservation == 1 ? '' : 's');
                $formError['count_' . $programId] .= " sind noch verfügbar.";
            }
        }
        $ticketsForSale = $maxReservation > 0;

        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $shoppingCart->setShoppingCartData($programId, $formData['countTickets'], $formData['countSaleTickets']);
            $formSuccess[] = 'Das Programm wurde erfolgreich dem Warenkorb hinzugefügt.';
        }

        return $this->getResponse([
            'formAction' => $this->getRoutePath('programDetail', ['id' => $programId]),
            'programData' => $programData,
            'countsNormal' => $countOfTickets,
            'countsSale' => $countOfTickets,
            'formData' => $formData,
            'successData' => $formSuccess,
            'errorData' => $formError,
            'ticketsForSale' => $ticketsForSale
        ]);
    }

}