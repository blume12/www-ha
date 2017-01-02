<?php

/**
 * User: Jasmin
 * Date: 25.11.2016
 */

namespace App\Helper\Menu;

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
     * @param array $config
     */
    public function addMenu($name, $route, $showAlways = true, $config = [])
    {
        $this->menuArray[] = array(
            'route' => $route,
            'name' => $name,
            'showAlways' => $showAlways
        );
        $lastIndex = count($this->menuArray) - 1;
        $this->menuArray[$lastIndex] = array_merge($this->menuArray[$lastIndex], $config);
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