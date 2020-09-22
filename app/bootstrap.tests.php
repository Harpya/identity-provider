<?php
declare(strict_types=1);

// error_reporting(E_ALL);

use Dotenv\Dotenv;

$rootPath = realpath('.');
require_once $rootPath . '/vendor/autoload.php';

/**
 * Load ENV variables
 */
Dotenv::createImmutable($rootPath, 'tests.conf')->load();
