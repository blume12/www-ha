<?php
/**
 * User: Jasmin
 * Date: 19.12.2016
 */

namespace App\Controller\Frontend;

use App\Model\ShoppingCart\ShoppingCart;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends FrontendController
{

    /**
     * Loads the content for the shopping cart list.
     *
     * @return Response
     */
    public function overviewAction()
    {
        $this->setTemplateName('shopping-cart-list');
        $this->setPageTitle('Warenkorb');

        $shoppingCart = new ShoppingCart($this->getConfig());

        $shoppingCartData = $shoppingCart->loadShoppingCartData();

        foreach ($shoppingCartData['list'] as $key => $value) {
            $shoppingCartData['list'][$key]['deleteLink'] = $this->getRoutePath('shoppingCartDelete',
                ['id' => $value['pid'], 'priceMode' => $value['priceMode']]);
        }
        return $this->getResponse([
            'shoppingCartData' => $shoppingCartData
        ]);
    }

    public function deleteAction()
    {
        $this->setTemplateName('shopping-cart-list');
        $this->setPageTitle('Warenkorb löschen');

        //TODO: delete the position.

        $shoppingCart = new ShoppingCart($this->getConfig());

        $shoppingCartData = $shoppingCart->loadShoppingCartData();

        foreach ($shoppingCartData['list'] as $key => $value) {
            $shoppingCartData['list'][$key]['deleteLink'] = $this->getRoutePath('shoppingCartDelete',
                ['id' => $value['pid'], 'priceMode' => $value['priceMode']]);
        }
        return $this->getResponse([
            'shoppingCartData' => $shoppingCartData
        ]);
    }
}