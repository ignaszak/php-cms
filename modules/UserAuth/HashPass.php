<?php

namespace UserAuth;

$baseDir = dirname(dirname(__DIR__));

require_once $baseDir . '/vendor/ircmaxell/password-compat/lib/password.php';

class HashPass
{

    private static $optionsArray = array();

    public static function configure(array $array)
    {
        self::$optionsArray = $array;
    }

    public static function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, self::$optionsArray);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

}
