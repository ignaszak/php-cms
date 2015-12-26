<?php

namespace AdminController\Post;

use FrontController\Controller;
use System\Server;
use Content\Controller\Factory;
use Content\Controller\PostController;
use Ignaszak\Registry\RegistryFactory;

class SavePostController extends Controller
{

    private $cms;

    public function __construct()
    {
        $this->cms = RegistryFactory::start()->get('cms');
    }

    public function run()
    {
        // Initialize
        $controller = new Factory(new PostController);

        // Find entity by id to update
        if ($_POST['id']) $controller->find($_POST['id']);

        $alias = $controller->getAlias($_POST['title']);
        $public = @$_POST['public'] == 1 ? 1 : 0;

        $controller
            // Sets data
            ->setReference('category', $this->getCategoryId())
            ->setReference('author', $this->cms->getUserId())
            ->setDate(new \DateTime)
            ->setTitle($_POST['title'])
            ->setAlias($alias)
            ->setContent($_POST['content'])
            ->setPublic($public)
            //Execute
            ->insert();

        Server::headerLocation("admin/post/p/edit/$alias");
    }

    private function getCategoryId(): int
    {
        if (!array_key_exists('categoryId', $_POST)) {
            $this->cms->setContent('category')->limit(1);
            $catArray = $this->cms->getContent();
            return $catArray[0]->getId();
        } else {
            return $_POST['categoryId'];
        }
    }

}