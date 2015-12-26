<?php

namespace ViewHelper;

use Conf\Conf;
use System\Router\Storage as Router;
use CMSException\InvalidClassException;
use Ignaszak\Registry\RegistryFactory;
use Content\Query\Content as Query;

class ViewHelper
{

    /**
     * @var ViewHelperExtension
     */
    private $_viewHelperExtension;

    /**
     * @var Conf
     */
    private $_conf;

    public function __construct()
    {
        $this->_viewHelperExtension = new ViewHelperExtension;
        $this->_conf = RegistryFactory::start('file')->register('Conf\Conf');
    }

    /**
     * Searches for correspond class based on method name.
     * If is found creates an object and call a method.
     * 
     * @param string $name
     * @param array $arguments
     * @throws InvalidClassException
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $extensionInstance = $this->_viewHelperExtension->getExtensionInstanceFromMethodName($name);

        if (method_exists($extensionInstance, $name)) {

            return call_user_func_array(array(
                $extensionInstance,
                $name
            ), $arguments);

        } else {

            throw new InvalidClassException("No class correspond to <b>$name</b> method");

        }
        return null;
    }

    /**
     * Returns current entity based on client request
     * 
     * @return array
     */
    public function display()
    {
        $_contentInstance = $this->_viewHelperExtension->getExtensionInstanceFromMethodName('Content');
        switch (Router::getRoute()) {
            case 'post':
                $_contentInstance->setContent('post');
                break;
            case 'category':
                $_contentInstance->setContent('post')
                    ->categoryId($this->getCategoryHierarchy(Router::getRoute('alias')))
                    ->force();
                break;
            case 'date':
                $_contentInstance->setContent('post')
                    ->date(Router::getRoute('date'))
                    ->force();
                break;
            default:
                $_contentInstance->setContent('post');
        }
        return $_contentInstance->getContent();
    }

    /**
     * @param string $alias
     * @param int $parentId
     * @return array
     */
    private function getCategoryHierarchy(string $alias = "", int $parentId = 0): array
    {
        $array = array();
        $query = new Query;
        $query->setContent('category')->paginate(false)->force();
        $categories = $query->getContent();
        if (!empty($alias)) {
            $query->setContent('category')->alias($alias)->force();
            $parentId = $query->getContent()[0]->getParentId();
        }
        foreach ($categories as $cat) {
            if ($parentId == $cat->getParentId()) {
                $array[] = $cat->getId();
                $array = array_merge($this->getCategoryHierarchy("", $cat->getId()), $array);
            }
        }
        return $array;
    }

}