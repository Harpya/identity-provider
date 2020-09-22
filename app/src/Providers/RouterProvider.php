<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\Router;

class RouterProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('router', function () {
            $router = new Router();

            // TODO : read the contents of files with routes

            $router->add(
                '/test',
                [
                    'controller' => 'index',
                    'action' => 'index',
                ]
            );

            return $router;
        });
    }
}
