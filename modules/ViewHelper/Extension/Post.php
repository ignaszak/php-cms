<?php

namespace ViewHelper\Extension;

use Content\Query\Content as Query;
use Content\Query\IContentQueryController;
use Ignaszak\Registry\RegistryFactory;
use System\Router\Storage as Router;

class Post
{

    /**
     * @var Query
     */
    private $_query;

    /**
     * @var \Entity\Posts[]
     */
    private $_post = array();

    /**
     * @var IContentQueryController
     */
    private $_postQuery;

    public function __construct()
    {
        $this->_query = new Query;
    }

    /**
     * @return boolean
     */
    public function havePost(): bool
    {
        if (empty($this->_post)) $this->selectPosts();
        return count($this->_post);
    }

    /**
     * @return \Entity\Posts[]
     */
    public function getPosts(): array
    {
        if (empty($this->_post)) $this->selectPosts();
        return $this->_post;
    }

    /**
     * @return IContentQueryController
     */
    public function setPostQuery(): IContentQueryController
    {
        $this->_postQuery = $this->_query->setContent('post')
            ->force()
            ->paginate(false);

        return $this->_postQuery;
    }

    /**
     * @return array
     */
    public function getPostQuery(): array
    {
        return $this->_postQuery->getContent();
    }

    private function selectPosts()
    {
        switch (Router::getRoute()) {

            case 'post':
                $this->_query->setContent('post');
                break;

            case 'category':
                $this->_query->setContent('post')
                    ->categoryId(
                        RegistryFactory::start()
                            ->register('System\Storage\CategoryList')->child()
                    )
                    ->force();
                break;

            case 'date':
                $this->_query->setContent('post')
                    ->date(Router::getRoute('date'))
                    ->force();
                break;

            default:
                $this->_query->setContent('post');
        }
        $this->_post = $this->_query->getContent();
    }

}
