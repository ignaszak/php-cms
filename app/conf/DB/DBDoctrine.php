<?php
namespace Conf\DB;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DBDoctrine
{

    /**
     *
     * @var EntityManager
     */
    private static $_em;

    /**
     *
     * @return EntityManager
     */
    public static function em()
    {
        if (empty(self::$_em)) {
            self::configure();
        }

        return self::$_em;
    }

    private static function configure()
    {
        self::setConnectionConfigurationAndEntityManager();
        self::registerEnumTypeAsDoctrineVarchar();
    }

    private static function setConnectionConfigurationAndEntityManager()
    {
        $paths = [dirname(dirname(__DIR__)) . '/model/Entity'];
        $isDevMode = true;

        $dbParams = [
            'driver' => 'pdo_mysql',
            'host' => DBSettings::DB_HOST,
            'user' => DBSettings::DB_USER,
            'password' => DBSettings::DB_PASSWORD,
            'dbname' => DBSettings::DB_NAME,
            'charset' => 'utf8'
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
        self::$_em = EntityManager::create($dbParams, $config);
    }

    private static function registerEnumTypeAsDoctrineVarchar()
    {
        $platform = self::$_em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
    }
}