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
use Phalcon\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Read the configuration
 */
class LogProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('domLogger', function () {
            $logger = new Logger('domain');
            $logger->pushHandler(new StreamHandler('/srv/logs/app/app.log', Logger::DEBUG));
            $logger->pushProcessor(new WebProcessor());
            $logger->pushProcessor(new IntrospectionProcessor());
            return $logger;
        });

        $di->setShared('sysLogger', function () {
            $logger = new Logger('system');
            $logger->pushHandler(new StreamHandler('/srv/logs/app/sys.log', Logger::DEBUG));
            return $logger;
        });
    }
}
