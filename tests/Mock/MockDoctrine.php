<?php
namespace Test\Mock;

use Conf\DB\DBDoctrine;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Test\Mock\MockTest;

class MockDoctrine
{

    /**
     * Creates connection to test data base
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $db
     * @param string $driver
     *            default pdo_mysql
     * @param string $charset
     *            default utf8
     */
    public static function connect(
        string $host,
        string $user,
        string $password,
        string $db,
        string $driver = 'pdo_mysql',
        string $charset = 'utf8'
    ) {
        // Configure Doctrine Entity Manager
        $paths = [dirname(dirname(__DIR__)) . '/modules/Entity'];
        $isDevMode = true;
        $dbParams = [
            'driver' => $driver,
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'dbname' => $db,
            'charset' => $charset
        ];
        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            $isDevMode,
            null,
            null,
            false
        );
        $em = EntityManager::create($dbParams, $config);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        // Insert Entity Manager to DBDoctrine
        self::mock($em);
    }

    /**
     * Simple facade to make every mockDoctrine::connect() the same in all tests
     * Please insert yours connection data to test database
     */
    public static function facadeConnection()
    {
        $mockDoctrineSettings = __DIR__ . '/MockDoctrineSettings.php';
        if (file_exists($mockDoctrineSettings)) {
            $connection = include $mockDoctrineSettings;
            self::connect(
                $connection['host'],
                $connection['user'],
                $connection['password'],
                $connection['db']
            );
        } else {
            throw new \Exception("File '{$mockDoctrineSettings}' not found.
Please create 'MockDoctrineSettings.php' file in '" . __DIR__ . "' directory with content:
<?php

return [
    'host'     => /* host adress */,
    'user'     => /* user name */,
    'password' => /* password */,
    'db'       => /* data base name */
];
");
        }
    }

    /**
     *
     * @param \PHPUnit_Framework_MockObject_Builder_Stub $stub
     */
    public static function mock($stub)
    {
        MockTest::injectStatic('Conf\DB\DBDoctrine', 'em', $stub);
    }

    /**
     * Clear mock entity from \Conf\DB\DBDoctrine
     */
    public static function clear()
    {
        MockTest::injectStatic('Conf\DB\DBDoctrine', 'em');
    }

    /**
     * Create mock createQueryBuilder method
     *
     * @param array $result
     * @return object
     */
    public static function queryBuilder(array $result)
    {
        $queryBuilder = \Mockery::mock('alias:\Doctrine\ORM\QueryBuilder');
        $queryBuilder->shouldReceive([
            'select' => $queryBuilder,
            'from' => $queryBuilder,
            'andwhere' => $queryBuilder,
            'getQuery' => $queryBuilder,
            'setFirstResult' => $queryBuilder,
            'setMaxResults' => $queryBuilder,
            'getSingleScalarResult' => $queryBuilder,
            'setParameter' => $queryBuilder,
            'orderBy' => $queryBuilder,
            'setContentQuery' => $queryBuilder,
            'findBy' => $queryBuilder,
            'getResult' => $result
        ]);
        return $queryBuilder;
    }

    /**
     *
     * @param array $result
     */
    public static function queryBuilderResult(array $result)
    {
        $em = \Mockery::mock('EntityManager');
        $em->shouldReceive([
            'createQueryBuilder' => self::queryBuilder($result),
            'find' => self::queryBuilder($result)
        ]);
        self::mock($em);
    }

    public static function repository(array $result)
    {
        $repository = \Mockery::mock('Repository');
        $repository->shouldReceive([
            'find' => $result,
            'findBy' => $result
        ]);
        return $repository;
    }

    /**
     *
     * @param array $result
     */
    public static function getRepositoryResult(array $result)
    {
        $em = \Mockery::mock('EntityManager');
        $em->shouldReceive('getRepository')->andReturn(self::repository($result));
        self::mock($em);
    }
}
