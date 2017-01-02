<?php
/**
 * User: Jasmin
 * Date: 19.12.2016
 */

namespace App\Controller\Frontend;

use App\Helper\Validator;
use App\Model\Reservation\Reservation;
use App\Model\ShoppingCart\ShoppingCart;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends FrontendController
{

    /**
     * Loads the content for the shopping cart list.
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request)
    {
        $this->setTemplateName('shopping-cart-list');
        $this->setPageTitle('Warenkorb');
        $this->setRequest($request);

        $shoppingCart = new ShoppingCart($this->getConfig());

        $shoppingCartData = $shoppingCart->loadShoppingCartData();
        if (isset($shoppingCartData['list'])) {
            foreach ($shoppingCartData['list'] as $key => $value) {
                $shoppingCartData['list'][$key]['deleteLink'] = $this->getRoutePath('shoppingCartDelete',
                    ['id' => $value['pid'], 'priceMode' => $value['priceMode']]);
            }
        }

        $formError = [];
        $formData = [];

        if ($request->getMethod() !== 'POST') {
            // Set default values


        } else {
            /* Check for errors */
            $formData = $this->getRequest()->request->all();

            foreach ($shoppingCartData['list'] as $key => $value) {
                if ($formData['count_' . $value['pid'] . '_' . $value['priceMode']] <= 0) {
                    $formError['count_' . $value['pid'] . '_' . $value['priceMode']] = 'Bitte geben Sie eine Anzahl für "' . $shoppingCartData['list'][$key]['title'] . '" (';
                    $formError['count_' . $value['pid'] . '_' . $value['priceMode']] .= ($shoppingCartData['list'][$key]['priceMode'] == 2 ? 'ermäßigter Preis' : 'normaler Preis') . ') ein.';
                    $shoppingCartData['list'][$key]['error'] = true;
                    $shoppingCartData['list'][$key]['count'] = $formData['count_' . $value['pid'] . '_' . $value['priceMode']];

                } else {
                    $shoppingCart->setShoppingCartDataItem($value['pid'], $value['priceMode'], $formData['count_' . $value['pid'] . '_' . $value['priceMode']]);
                }
            }

            $shoppingCartData = $shoppingCart->loadShoppingCartData();

            if (!Validator::isAlpha($formData['firstname'], true)) {
                $formError['firstname'] = 'Bitte geben Sie einen Vornamen ein.';
            }

            if (!Validator::isAlpha($formData['lastname'], true)) {
                $formError['lastname'] = 'Bitte geben Sie einen Nachnamen ein.';
            }
            //TODO: check Email
            if (!Validator::isAlpha($formData['email'], true)) {
                $formError['email'] = 'Bitte geben Sie einen E-Mail-Adresse ein.';
            }
        }

        // Handle valid post
        if ($request->getMethod() == 'POST' && count($formError) <= 0) {
            /* Save data */
            $reservation = new Reservation($this->getConfig());
            $data = [
                'firstname' => $formData['firstname'],
                'lastname' => $formData['lastname'],
                'email' => $formData['email']
            ];

            foreach ($shoppingCartData['list'] as $key => $value) {
                $data['program'][] = [
                    'PId' => $value['pid'],
                    'priceMode' => $value['priceMode'],
                    'countTickets' => $formData['count_' . $value['pid'] . '_' . $value['priceMode']],
                    'price' => $value['price']

                ];
            }


            $reservation->saveData($data);
        }

        return $this->getResponse([
            'shoppingCartData' => $shoppingCartData,
            'errorData' => $formError
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $this->setTemplateName('shopping-cart-list');
        $this->setPageTitle('Warenkorb löschen');

        $this->setRequest($request);
        $programId = $this->getRequest()->attributes->get('id');
        $priceMode = $this->getRequest()->attributes->get('priceMode');

        $shoppingCart = new ShoppingCart($this->getConfig());

        $shoppingCart->deleteShoppingCartItem($programId, $priceMode);

        $shoppingCartData = $shoppingCart->loadShoppingCartData();

        if (isset($shoppingCartData['list'])) {
            foreach ($shoppingCartData['list'] as $key => $value) {
                $shoppingCartData['list'][$key]['deleteLink'] = $this->getRoutePath('shoppingCartDelete',
                    ['id' => $value['pid'], 'priceMode' => $value['priceMode']]);
            }
        } else {
            $shoppingCartData = null;
        }

        return new RedirectResponse($this->getRoutePath('shoppingCartList'));
    }
}