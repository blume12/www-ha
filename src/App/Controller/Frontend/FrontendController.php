<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 13:20
 */

namespace App\Controller\Frontend;


use App\Controller\Controller;
use App\Helper\Menu\Menu;
use App\Model\ShoppingCart\ShoppingCart;

abstract class FrontendController extends Controller
{
    /**
     * String to the frontend templates.
     *
     * @var string
     */
    protected $path = '/../../../templates/frontend/';

    /**
     * FrontendController constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $config['frontend'] = true;
        parent::__construct($config);

        $menu = new Menu();
        $menu->addMenu('Startseite', $this->getRoutePath('startPage'));
        $menu->addMenu('Programme', $this->getRoutePath('programs'));
        $shoppingCart = new ShoppingCart($this->getConfig());

        $count = 0;
        $shoppingCartData = $shoppingCart->loadShoppingCartData();
        if(isset($shoppingCartData['list'])) {
            $count = count($shoppingCartData['list']);
        }
        $menu->addMenu($count, $this->getRoutePath('shoppingCartList'), true, ['className' => 'right', 'image' => '/images/shopping-cart.png']);
        $this->menuArray = $menu->getMenuArray();
    }
}