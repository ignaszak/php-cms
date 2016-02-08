<?php
namespace Controller;

use FrontController\Controller as FrontController;
use Ignaszak\Registry\RegistryFactory;
use System\Server;

class SendMailController extends FrontController
{

    public function run()
    {
        $adminMail = RegistryFactory::start('file')->register('Conf\Conf')
            ->getAdminEmail();

        $transport = \Swift_MailTransport::newInstance();

        $message = \Swift_Message::newInstance()->setSubject('Message from CMS')
            ->setFrom($_POST['from'])
            ->setTo($adminMail)
            ->setBody($_POST['body']);

        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);

        Server::setReferData(['send' => 1]);
        Server::headerLocationReferer();
    }
}
