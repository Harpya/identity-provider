<?php
declare(strict_types=1);

error_reporting(E_ALL & ~E_NOTICE);

$rootPath = realpath('.');
require_once $rootPath . '/vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Load ENV variables
 */
if (file_exists(($rootPath . 'tests.conf'))) {
    Dotenv::createImmutable($rootPath, 'tests.conf')->load();
} else {
    die("File $rootPath/tests.conf does not exists ");
}
