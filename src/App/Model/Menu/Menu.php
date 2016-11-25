<?php

/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Model\Menu;

class Menu
{

    /**
     * @var array
     */
    private $menuArray = [];

    /**
     * Add a main menu to the menu array.
     *
     * @param $name
     * @param $route
     * @param bool $showAlways
     */
    public function addMenu($name, $route, $showAlways = true)
    {
        $this->menuArray[] = array(
            'route' => $route,
            'name' => $name,
            'showAlways' => $showAlways
        );
    }

    /**
     * Return the menu data array.
     *
     * @return array
     */
    public function getMenuArray()
    {
        return $this->menuArray;
    }
}