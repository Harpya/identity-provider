<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$rootPath = realpath(__DIR__);

if (file_exists($rootPath . '/.env')) {
    /**
     * Load ENV variables
     */
    Dotenv::createImmutable($rootPath)->load();
}

$defaultConfig = [
    'adapter' => 'pgsql',
    'host' => getenv('DB_HOST'),
    'user' => getenv('DB_USERNAME'),
    'pass' => getenv('DB_PASSWORD'),
    'port' => 5432,
    'charset' => 'utf8',
    'name' => getenv('DB_DBNAME'),
];

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds' => __DIR__ . '/db/seeds'
    ],
    'version_order' => 'creation',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'production' => array_merge(
            $defaultConfig,
            [
                //
            ]
        ),
        'development' => array_merge(
            $defaultConfig,
            [
                //
            ]
        ),
        'testing' => array_merge(
            $defaultConfig,
            [
                //
            ]
        ),
    ]
];
