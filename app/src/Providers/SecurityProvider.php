<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Harpya\IP\Helpers\SecurityHelper;

class SecurityProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'security',
            function () {
                $security = new SecurityHelper();

                // Set the password hashing factor to 12 rounds
                $security->setWorkFactor(12);

                return $security;
            }
        );
    }
}
