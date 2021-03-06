<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Session\Bag;

class SessionBagProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('sessionBag', function () {
            return new Bag('authData');
        });
    }
}
