<?php
namespace AdminController\Post;

use FrontController\Controller as FrontController;
use App\Resource\Server;
use FrontController\ViewHelperController;
use DataBase\Controller\Controller;
use Entity\Posts;

class EditPostController extends FrontController
{

    /**
     *
     * @var string
     */
    public $action;

    /**
     *
     * @var string
     */
    public $alias;

    public function run()
    {
        $this->action = $this->http->router->get('action');
        $this->alias = $this->http->router->get('alias');
        $this->view->addView('theme/post-edit.html');

        if ($this->action == 'delete' && $this->alias) {
            $controller = new Controller(new Posts());
            $controller->findOneBy([
                'alias' => $this->alias
            ])->remove();

            Server::headerLocation(
                $this->url('admin-post-list', [
                    'action' => 'view', 'page' => 1
                ])
            );
        }
    }

    /**
     *
     * @return ViewHelperController
     */
    public function setViewHelper()
    {
        return new class($this) extends ViewHelperController
        {

            /**
             *
             * @var array
             */
            private $returnData = [];

            /**
             *
             * @var array
             */
            private $data = [];

            /**
             *
             * @param FrontController $_controller
             */
            public function __construct(FrontController $_controller)
            {
                parent::__construct($_controller);
                $this->returnData = $this->_controller->view
                    ->getFormResponseData('data');
                $this->setData();
            }

            /**
             *
             * @param string $key
             * @return mixed
             */
            public function getAdminPost(string $key)
            {
                return $this->data[$key] ?? null;
            }

            private function setData()
            {
                $data = [];
                $data['id'] = null;
                $data['title'] = $this->returnData['setTitle'];
                $data['content'] = $this->returnData['setContent'];
                $data['catId'] = $this->returnData['setCategory'];
                $data['public'] = $this->returnData['setPublic'];
                $data['formTitle'] = 'Add new post';
                $data['formLink'] = $this->_controller->url('admin-post-save', [
                    'action' => 'form'
                ]);

                if ($this->_controller->action == 'edit' && $this->_controller->alias) {
                    $data['formTitle'] = 'Edit post';

                    $this->_controller->query->setQuery('post')
                        ->alias($this->_controller->alias);

                    foreach ($this->_controller->query->getQuery() as $post) {
                        $data['id'] = $post->getId();
                        $data['catId'] = $post->getCategory()->getId();
                        $data['title'] = $post->getTitle();
                        $data['content'] = $post->getContent();
                        $data['public'] = $post->getPublic();
                        $data['deleteLink'] = $this->_controller->url(
                            'admin-post-edit',
                            ['action' => 'delete', 'alias' => $post->getAlias()]
                        );
                    }
                }
                $this->data = $data;
            }
        };
    }
}
