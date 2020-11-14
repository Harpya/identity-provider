<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $dbConfig = $di->getShared('config')->get('database')->toArray();
        $di->setShared('db', function () use ($dbConfig) {
            $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $dbConfig['adapter'];
            unset($dbConfig['adapter']);

            $db = new $dbClass($dbConfig);

            if (is_null($db)) {
                throw new \Exception('Error connecting DB');
            }
            return $db;
        });
    }
}
