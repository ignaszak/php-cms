<?php
namespace AdminController\Menu;

use FrontController\Controller;
use FrontController\ViewHelperController;

class EditMenuController extends Controller
{

    public function run()
    {
        $this->view()->addView('theme/menu-edit.html');
        $this->setViewHelperName('AdminLoadMenu');
    }

    /**
     *
     * @return ViewHelperController
     */
    public function setViewHelper()
    {
        return new class($this) extends ViewHelperController {

            /**
             *
             * @var integer
             */
        private $id;

        public function __construct(Controller $_controller)
        {
            parent::__construct($_controller);
            $this->id = $this->_controller->view()->getRoute('id');
        }

        public function getAdminLoadMenuFormTitle(): string
        {
            return $this->_controller->view()->getRoute('action') == 'edit' ? "Edit menu" : "Create new menu";
        }

        public function getAdminLoadMenuId()
        {
            return empty($this->id) ? "" : $this->id;
        }

        public function getAdminLoadMenuName(): string
        {
            $this->_controller->query()
                ->setContent('menu')
                ->id($this->id)
                ->limit(1)
                ->paginate(false)
                ->force();
            $content = $this->_controller->query()->getContent();
            return count($content) ? $content[0]->getName() : "";
        }

        public function getAdminLoadMenuLink(): string
        {
            return empty($this->id) ? "" : $this->_controller->view()->getAdminAdress() . '/menu/ajax/' . $this->id . '/edit.json';
        }

        public function getAdminLoadMenuDelete(): string
        {
            return $this->_controller->view()->getAdminAdress() . '/menu/delete/' . $this->id;
        }
        };
    }
}
