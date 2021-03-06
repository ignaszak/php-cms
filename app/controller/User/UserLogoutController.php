<?php
namespace Controller\User;

use FrontController\Controller as FrontController;
use App\Resource\Server;
use Ignaszak\Registry\RegistryFactory;

class UserLogoutController extends FrontController
{

    public function run()
    {
        if ($this->registry->get('user')->isUserLoggedIn()) {
            RegistryFactory::start('cookie')->remove('userSession');
            RegistryFactory::start('session')->remove('userSession');
        }
        Server::setReferData([
            'search' => Server::getReferData()['search']
        ]);
        Server::headerLocationReferer();
    }
}
