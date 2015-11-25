<?php

define('USER_ACTION', System\Router\Storage::getRoute('userAction')) ;

$userLogin      = @$_POST['userLogin'];
$userEmail      = @$_POST['userEmail'];
$userPassword   = @$_POST['userPassword'];
$userRePassword = @$_POST['userRePassword'];
$userRemember   = @$_POST['userRemember'];


switch (USER_ACTION) {
    case 'login':
        $user->login($userLogin, $userPassword, $userRemember);
        break;
    case 'logout':
        $user->logout();
        break;
    case 'registration':
        $user->registration($userLogin, $userEmail, $userPassword, $userRePassword);
        break;
}

if (USER_ACTION) System\Server::headerLocationReferer();
