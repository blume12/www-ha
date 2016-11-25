<?php

/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Model\Menu;

class Menu
{

    private $menuArray = [];

    public function addMenu($name, $route, $showAlways = true) {
        $this->menuArray[] =  array(
            'route' => $route,
            'name' => $name,
            'showAlways' => $showAlways
        );
    }

    public function getMenuArray() {
        return $this->menuArray;
    }
}