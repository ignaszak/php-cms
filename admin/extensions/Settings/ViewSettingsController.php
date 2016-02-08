<?php
namespace AdminController\Settings;

use FrontController\Controller as FrontController;
use FrontController\ViewHelperController;

class ViewSettingsController extends FrontController
{

    public function run()
    {
        $this->view()->addView('theme/settings-view.html');
        $this->setViewHelperName('AdminSettings');
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
             * @var \Entity\Options
             */
        private $_options;

            /**
             *
             * @param Controller $_controller
             */
        public function __construct(FrontController $_controller)
        {
            parent::__construct($_controller);
            $this->_controller->query()->setContent('options');
            $this->_options = $this->_controller->query()->getContent()[0];
        }

            /**
             *
             * @return string
             */
        public function getAdminSettingsFormAction(): string
        {
            return $this->_controller->view()->getAdminAdress() . "/settings/save";
            ;
        }

            /**
             *
             * @return \Entity\Options
             */
        public function getAdminSettings(): \Entity\Options
        {
            return $this->_options;
        }

            /**
             *
             * @return array
             */
        public function getAdminSettingsThemesList(): array
        {
            $baseDir = dirname(dirname(dirname(__DIR__)));
            return glob($baseDir . "/themes/*", GLOB_ONLYDIR);
        }
        };
    }
}
