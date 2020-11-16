<?php

declare(strict_types=1);

include_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
use Dotenv\Dotenv;

$container = new CliDI();

$rootPath = realpath(__DIR__ . '/..');

if (file_exists($rootPath . '/.env')) {
    /**
     * Load ENV variables
     */
    Dotenv::createImmutable($rootPath)->load();
}

$container->offsetSet('rootPath', function () use ($rootPath) {
    return $rootPath;
});

$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('Harpya\CLI\Tasks');
$container->setShared('dispatcher', $dispatcher);

$providers = [
    \Harpya\IP\Providers\ConfigProvider::class,
    \Harpya\IP\Providers\DatabaseProvider::class,
];

foreach ($providers as $provider) {
    $container->register(new $provider());
}
