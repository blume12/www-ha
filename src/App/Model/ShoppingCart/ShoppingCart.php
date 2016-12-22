<?php
/**
 * User: Jasmin
 * Date: 22.12.2016
 */

namespace App\Model\ShoppingCart;


use App\Helper\Formatter;
use App\Helper\Session;
use App\Model\Database\DbBasis;
use App\Model\Program\Program;
use App\Model\Program\ProgramPrice;

class ShoppingCart extends DbBasis
{

    /**
     * @var string
     */
    private static $sessionName = "shoppingCart";

    /**
     * @var $shoppingCartSession Session
     */
    private $shoppingCartSession;

    /**
     * ShoppingCart constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->initSession();
    }

    /**
     * Initialize the session.
     */
    private function initSession()
    {
        $this->shoppingCartSession = new Session();

    }

    /**
     * Return the session data for the shopping cart.
     *
     * @return bool | array
     */
    private function getSessionData()
    {
        return $this->shoppingCartSession->getSessionByKey(self::$sessionName);
    }

    /**
     * Load the shopping cart data for the output.
     *
     * @return array
     */
    public function loadShoppingCartData()
    {
        $shoppingCartData = [];
        $shoppingCartData['priceTotal'] = 0;
        $program = new Program($this->getConfig());
        $programPrice = new ProgramPrice($this->getConfig());
        $i = 0;
        foreach ($this->getSessionData() as $key => $count) {
            if ($count > 0) {
                $tmp = explode('_', $key);
                $programId = $tmp[0];
                $priceMode = $tmp[1];
                $programData = $program->loadSpecificEntry($programId);


                $shoppingCartData['list'][$i]['count'] = $count;
                $shoppingCartData['list'][$i]['title'] = $programData['title'];

                $simplePrice = $programPrice->getPriceByMode($priceMode, $programData['price']);

                $simplePrice = str_replace(',', '.', $simplePrice);

                $shoppingCartData['list'][$i]['price'] = Formatter::formatPrice($simplePrice);
                $shoppingCartData['list'][$i]['priceTotal'] = Formatter::formatPrice($simplePrice * $count);

                $shoppingCartData['priceTotal'] += $simplePrice * $count;
                $i++;
            }
        }
        $shoppingCartData['priceTotal'] = Formatter::formatPrice($shoppingCartData['priceTotal']);

        return $shoppingCartData;
    }
}