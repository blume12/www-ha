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
use App\Model\Reservation\Reservation;

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
     * @param $programId
     * @param $priceMode
     */
    public function deleteShoppingCartItem($programId, $priceMode)
    {
        $this->shoppingCartSession->removeSessionByKeyItem(self::$sessionName, $programId . '_' . $priceMode);

    }

    /**
     * Delete all shopping cart data from the session.
     */
    public function deleteAllShoppingCartData()
    {
        $this->shoppingCartSession->removeSessionByKey(self::$sessionName);

    }

    /**
     * @param $programId
     * @param $countTickets
     * @param $countSaleTickets
     */
    public function setShoppingCartData($programId, $countTickets, $countSaleTickets)
    {
        $shoppingCartData = $this->shoppingCartSession->getSessionByKey('shoppingCart');
        if (!$shoppingCartData) {
            $shoppingCartData = [$programId . "_1" => $countTickets, $programId . "_2" => $countSaleTickets];
        } else {
            $shoppingCartData[$programId . "_1"] = $countTickets;
            $shoppingCartData[$programId . "_2"] = $countSaleTickets;
        }

        $this->shoppingCartSession->setSession('shoppingCart', $shoppingCartData);

    }

    /**
     * @param $programId
     * @param $priceMode
     * @param $count
     */
    public function setShoppingCartDataItem($programId, $priceMode, $count)
    {
        $shoppingCartData = $this->shoppingCartSession->getSessionByKey('shoppingCart');
        if (!$shoppingCartData) {
            $shoppingCartData = [$programId . "_" . $priceMode => $count];
        } else {
            $shoppingCartData[$programId . "_" . $priceMode] = $count;
        }

        $this->shoppingCartSession->setSession('shoppingCart', $shoppingCartData);

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
        $sessionData = $this->getSessionData();
        if ($sessionData) {
            foreach ($sessionData as $key => $count) {
                if ($count > 0) {
                    $tmp = explode('_', $key);
                    $programId = $tmp[0];
                    $priceMode = $tmp[1];
                    $programData = $program->loadSpecificEntry($programId);
                    if ($programData) {
                        $shoppingCartData['list'][$i]['pid'] = $programData['PId'];
                        $shoppingCartData['list'][$i]['count'] = $count;
                        $shoppingCartData['list'][$i]['title'] = $programData['title'];
                        $shoppingCartData['list'][$i]['priceMode'] = $priceMode;

                        $simplePrice = $programPrice->getPriceByMode($priceMode, $programData['price']);

                        $simplePrice = str_replace(',', '.', $simplePrice);

                        $shoppingCartData['list'][$i]['price'] = Formatter::formatPrice($simplePrice);
                        $shoppingCartData['list'][$i]['priceTotal'] = Formatter::formatPrice($simplePrice * $count);

                        $shoppingCartData['priceTotal'] += $simplePrice * $count;

                        $i++;
                    }
                }
            }
            $shoppingCartData['priceTotal'] = Formatter::formatPrice($shoppingCartData['priceTotal']);
        }
        return $shoppingCartData;
    }

    // TODO: Refactor the method!!!
    public function loadShoppingCartDataFromDB($id)
    {
        $shoppingCartData = [];
        $shoppingCartData['priceTotal'] = 0;
        $program = new Program($this->getConfig());
        $programPrice = new ProgramPrice($this->getConfig());
        $i = 0;
        $reservation = new Reservation($this->getConfig());
        $reservationData = $reservation->searchData($id, false);
        if ($reservationData) {
            $shoppingCartData['priceTotal'] = Formatter::formatPrice($shoppingCartData['priceTotal']);
            foreach ($reservationData as $key => $data) {
                $programId = $data['PId'];
                $priceMode = $data['priceMode'];
                $programData = $program->loadSpecificEntry($programId);
                if ($programData) {
                    $shoppingCartData['list'][$i]['pid'] = $programData['PId'];
                    $shoppingCartData['list'][$i]['count'] = $data['countTickets'];
                    $shoppingCartData['list'][$i]['title'] = $programData['title'];
                    $shoppingCartData['list'][$i]['priceMode'] = $priceMode;

                    $simplePrice = $programPrice->getPriceByMode($priceMode, $programData['price']);

                    $simplePrice = str_replace(',', '.', $simplePrice);

                    $shoppingCartData['list'][$i]['price'] = Formatter::formatPrice($simplePrice);
                    $shoppingCartData['list'][$i]['priceTotal'] = Formatter::formatPrice($simplePrice * $data['countTickets']);

                    $shoppingCartData['priceTotal'] += $simplePrice * $data['countTickets'];

                    $i++;
                }

            }
            $shoppingCartData['priceTotal'] = Formatter::formatPrice($shoppingCartData['priceTotal']);
        }
        return $shoppingCartData;
    }
}