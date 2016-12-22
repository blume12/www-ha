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
        return $this->getResponse([
            'shoppingCartData' => $shoppingCartData
        ]);
    }
}