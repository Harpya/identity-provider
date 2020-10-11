<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Harpya\IP\Lib\SessionManager;

/**
 * Start the session the first time some component request the session service
 */
class SessionProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('session', function () use ($di) {
            $session = new SessionManager();

            $dbConfig = $di->getShared('config')->get('database')->toArray();

            $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $dbConfig['adapter'];
            unset($dbConfig['adapter']);

            $pdo = new $dbClass($dbConfig);

            $dbSession = new \Harpya\IP\Lib\DBSessionAdapter($pdo);

            $session->setAdapter($dbSession);

            $session->start();

            return $session;
        });
    }
}
