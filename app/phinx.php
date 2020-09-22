<?php

define('AGENT_CLI', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$rootPath = realpath('.');

    /**
     * Load ENV variables
     */
    Dotenv::createImmutable($rootPath)->load();

$defaultConnectionConfig = [
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
        // Production section
        'production' => array_merge(
            $defaultConnectionConfig,
            [
                //
            ]
        ),
        // Development section
        'development' => array_merge(
            $defaultConnectionConfig,
            [
                //
            ]
        ),
        // Testing section (integration tests)
        'testing' => array_merge(
            $defaultConnectionConfig,
            [
                //
            ]
        ),
    ]
];
