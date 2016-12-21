<?php
/**
 * User: Jasmin
 * Date: 19.12.2016
 */

namespace App\Controller\Frontend;


use App\Helper\Formatter;
use App\Helper\Session;
use App\Model\Program\Program;
use App\Model\Program\ProgramPrice;
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


        // TODO: the next few lines should be a model ;)
        $session = new Session();
        $sessionData = $session->getSessionByKey('shoppingCart');

        // var_dump($sessionData);


        $shoppingCartData = [];
        $shoppingCartData['priceTotal'] = 0;
        $program = new Program($this->getConfig());
        $programPrice = new ProgramPrice($this->getConfig());
        $i = 0;
        foreach ($sessionData as $key => $count) {
            if ($count > 0) {
                $tmp = explode('_', $key);
                $programId = $tmp[0];
                $priceMode = $tmp[1];
                $programData = $program->loadSpecificEntry($programId);
                $programPriceData = $programPrice->loadSpecificEntry($programData['price']);

                $shoppingCartData['list'][$i]['count'] = $count;
                $shoppingCartData['list'][$i]['title'] = $programData['title'];
                switch ($priceMode) {
                    case 0:
                        $simplePrice = $programPriceData['price'];
                        break;
                    case 1:
                        $simplePrice = $programPriceData['priceReduce'];
                        break;
                    default:
                        $simplePrice = 3; // TODO: This is a very hot fix for tomorrow
                        break;
                }
                $simplePrice = str_replace(',', '.', $simplePrice);

                $shoppingCartData['list'][$i]['price'] = Formatter::formatPrice($simplePrice);
                $shoppingCartData['list'][$i]['priceTotal'] = Formatter::formatPrice($simplePrice * $count);

                $shoppingCartData['priceTotal'] += $simplePrice * $count;
                $i++;
            }
        }
        $shoppingCartData['priceTotal'] = Formatter::formatPrice($shoppingCartData['priceTotal']);

        return $this->getResponse([
            'shoppingCartData' => $shoppingCartData
        ]);
    }
}