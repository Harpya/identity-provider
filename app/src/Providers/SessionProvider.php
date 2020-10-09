<?php
declare(strict_types=1);

/**
 * This file is part of the Invo.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;

/**
 * Start the session the first time some component request the session service
 */
class SessionProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('session', function () use ($di) {
            $session = new SessionManager();
            // $files = new SessionAdapter([
            //     'savePath' => sys_get_temp_dir(),
            // ]);
            // $session->setAdapter($files);

            $dbConfig = $di->getShared('config')->get('database')->toArray();

            $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $dbConfig['adapter'];
            unset($dbConfig['adapter']);

            $pdo = new $dbClass($dbConfig);

            // //'col_sessid' => 'sess_id', 'col_data' => 'sess_data', 'col_last_activity' => 'last_activity'
            $dbSession = new \Harpya\IP\Lib\DBSessionAdapter(
                $pdo
            );

            $session->setAdapter($dbSession);

            $session->start();

            return $session;
        });
    }
}
