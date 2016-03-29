<?php
namespace App\Resource;

use App\Resource\RouterStatic as Router;
use DataBase\Query\Query;
use Ignaszak\Registry\RegistryFactory;

class CategoryList
{

    /**
     *
     * @var Query
     */
    private $_query;

    /**
     *
     * @var \Entity\Categories[]
     */
    private $categoryArray;

    public function __construct()
    {
        $this->_query = RegistryFactory::start()
            ->register('DataBase\Query\Query');
        $this->_query->setQuery('category');
        $this->categoryArray = $this->_query->getStaticQuery();
    }

    /**
     *
     * @param string $alias
     * @return integer
     */
    public function getIdByAlias(string $alias): int
    {
        $this->_query->setQuery('category')
            ->alias($alias);
        $content = $this->_query->getStaticQuery();
        return $content ? $content[0]->getId() : 0;
    }

    /**
     *
     * @return \Entity\Categories[]
     */
    public function get(): array
    {
        return $this->categoryArray;
    }

    /**
     *
     * @param string $alias
     * @return \Entity\Categories[]
     */
    public function child(string $alias = null, int $parentId = 0): array
    {
        $array = [];
        $alias = $alias ?? Router::getParam('alias');
        if (! empty($alias)) {
            $parentId = $this->getIdByAlias($alias);
            $array[] = $parentId;
        }
        foreach ($this->categoryArray as $cat) {
            if ($parentId == $cat->getParentId()) {
                $array[] = $cat->getId();
                $array = array_merge($this->child("", $cat->getId()), $array);
            }
        }
        return $array;
    }
}
