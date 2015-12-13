<?php

defined('ACCESS') or die();

$baseDir = dirname(dirname(__DIR__));

if (!$user->isUserLoggedIn()) {
    require $baseDir . '/' . ADMIN_FOLDER . '/extensions/Index/login.html';
    exit;
}

if ($cms->getUserRole() != 'admin') {
    System\Server::headerLocation('');
}

Display\DisplayExtension::addExtensionClass(array(
    'Admin\\Extension\\Display\\Admin'
));
