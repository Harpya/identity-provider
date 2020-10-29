<?php
declare(strict_types=1);

use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => getenv('DB_ADAPTER'),
        'host' => getenv('DB_HOST'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'dbname' => getenv('DB_DBNAME'),
        // 'charset' => getenv('DB_CHARSET'),
    ],
    'application' => [
        'viewsDir' => getenv('VIEWS_DIR'),
        'baseUri' => getenv('BASE_URI'),
        'siteName' => getenv('APP_SITE_NAME'),
        'siteURL' => getenv('APP_SITE_URL'),
        'customerServiceEmail' => getenv('APP_CUSTOMER_SERVICE_EMAIL'),
        'hipBaseURL' => getenv('HIP_HOSTNAME'),
    ],
    'communication' => [
        'mail' => [
            'host' => getenv('MAIL_HOST'),
            'port' => getenv('MAIL_PORT'),
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD'),
            'fromEmail' => getenv('MAIL_FROM_EMAIL'),
            'fromName' => getenv('MAIL_FROM_NAME'),
        ]
    ],
    'features' => [
    ],
]);
