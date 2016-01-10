<?php

namespace Menu;

use Content\Query\Content as Query;
use Ignaszak\Registry\RegistryFactory;

class Menu
{

    /**
     * @var Query
     */
    private $_query;

    /**
     * @var \Conf\Conf
     */
    private $_conf;

    /**
     * @var \Entity\MenuItems[]
     */
    private $menuItemsArray = array();

    public function __construct()
    {
        $this->_query = new Query;
        $this->_conf = RegistryFactory::start('file')->register('Conf\Conf');
    }

    /**
     * @param string $position
     * @return string
     */
    public function getMenu(string $position, string $class = ""): string
    {
        $this->loadMenuItmsByPosition($position);
        return $this->createMenu($class);
    }

    /**
     * @param string $position
     */
    private function loadMenuItmsByPosition(string $position)
    {
        $this->_query->setContent('menu')
            ->findBy('position', $position)
            ->limit(1)
            ->paginate(false)
            ->force();
        $content = $this->_query->getContent();

        if (array_key_exists(0, $content))
            $this->menuItemsArray = $content[0]->getMenuItems();
    }

    private function createMenu(string $class): string
    {
        $string = "<ul {$class}>";
        foreach ($this->menuItemsArray as $item) {
            $string .= "<li><a href=\"{$this->validAdress($item->getAdress())}\">";
            $string .= "{$item->getTitle()}</a></li>";
        }
        $string .= "</ul>";
        return count($this->menuItemsArray) ? $string : "";
    }

    /**
     * @param string $adress
     * @return string
     */
    private function validAdress(string $adress): string
    {
        if (!filter_var($adress, FILTER_VALIDATE_URL) === false) {
            return $adress;
        } else {
            return $this->_conf->getBaseUrl() . $adress;
        }
    }

}