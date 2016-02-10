<?php
namespace Controller\User;

use FrontController\Controller as FrontController;
use System\Server;
use UserAuth\HashPass;
use Content\Controller\Controller;
use Entity\Users;
use Ignaszak\Registry\RegistryFactory;

class UserSaveController extends FrontController
{

    public function run()
    {
        $_user = RegistryFactory::start()->get('user');
        $this->checkIfUserIsLogged($_user);

        $userId = $_user->getUserSession()->getId();
        $controller = new Controller(new Users());
        $controller->find($userId);

        if (!empty($_POST['userPassword'])) {
            Server::setReferData(['form' => 'accountPassword']);
            $this->savePassword($controller);
        } else {
            Server::setReferData(['form' => 'accountData']);
            $this->saveData($controller);
        }

        $this->reloadUserSession($controller->entity());

        Server::setReferData(['accountSuccess' => 1]);
        Server::headerLocationReferer();
    }

    private function checkIfUserIsLogged(\UserAuth\User $_user)
    {
        if (! $_user->isUserLoggedIn()) {
            Server::headerLocationReferer();
        }
    }

    /**
     *
     * @param Controller $controller
     */
    private function savePassword(Controller $controller)
    {
        $hash = $controller->entity()->getPassword();
        if (! HashPass::verifyPassword($_POST['userPassword'], $hash)) {
            Server::setReferData([
                'error' => ['incorrectPassword' => 1]
            ]);
            Server::headerLocationReferer();
        } else {
            $controller->setPassword($_POST['userNewPassword'])->update();
        }
    }

    /**
     *
     * @param Controller $controller
     */
    private function saveData(Controller $controller)
    {
        $controller->setEmail($_POST['userEmail'])
            ->update([
                'Email'
            ]);
    }

    private function reloadUserSession(\Entity\Users $_userEntity)
    {
        if (RegistryFactory::start('session')->get('userSession')) {
            RegistryFactory::start('session')->set('userSession', $_userEntity);
        } else {
            RegistryFactory::start('cookie')->set('userSession', $_userEntity);
        }
    }
}