<?php
namespace Module\CategoryList;

use Ignaszak\Registry\RegistryFactory;

class CategoryList
{

    private $_conf;

    private $categoryList;

    public function __construct()
    {
        $this->_conf = RegistryFactory::start('file')->register('Conf\Conf');
        $this->categoryList = RegistryFactory::start()
            ->register('App\Resource\CategoryList')->get();
    }

    public function getCategoryList(string $arg, int $parentId = 1): string
    {
        $string = "<ul {$arg}>";
        if (array_key_exists(0, $this->categoryList)) {
            $string .= "<li><a href=\"{$this->_conf->getBaseUrl()}\">";
            $string .= "{$this->categoryList[0]->getTitle()}</a>";
            $string .= "</li>";
        }
        $categoriesExists = count($this->categoryList);
        unset($this->categoryList[0]);
        foreach ($this->categoryList as $cat) {
            if ($parentId == $cat->getParentId()) {
                $string .= "<li><a href=\"{$this->_conf->getBaseUrl()}" .
                    "category/{$cat->getAlias()}\">";
                $string .= "{$cat->getTitle()}</a>";
                $string .= $this->getCategoryList($arg, $cat->getId());
                $string .= "</li>";
            }
        }
        $string .= "</ul>";
        return $categoriesExists ? $string : "";
    }
}