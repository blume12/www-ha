<?php
/**
 * Author: Jasmin Stern
 * Date: 23.11.2016
 * Time: 13:20
 */

namespace App\Controller\Frontend;


use App\Controller\Controller;
use App\Helper\Menu\Menu;

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
        parent::__construct($config);

        $menu = new Menu();
        $menu->addMenu('Startseite', $this->getRoutePath('startPage'));
        $menu->addMenu('Programme', $this->getRoutePath('programs'));
        $menu->addMenu('Impressum', $this->getRoutePath('siteNotice'));
        // TODO: Set the documentation link to footer
        $menu->addMenu('Dokumentation', $this->getRoutePath('documentation'));
        $menu->addMenu('Login', $this->getRoutePath('adminLogin'));
        $this->menuArray = $menu->getMenuArray();
    }
}