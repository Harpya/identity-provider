<?php
declare(strict_types=1);

namespace Harpya\IP;

use \Harpya\IP\Exceptions\ApplicationExection;

class Application
{
    protected static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            // if (is_null($di)) {
            //     throw new ApplicationExection('Falat error - incorrect use of getApplication');
            // }
            static::$instance = new Application();
        }
        return static::$instance;
    }

    /**
     * Retrieve the final target, from original initial request
     */
    public function getTargetURL()
    {
        return getenv('APP_DEFAULT_TARGET_AFTER_LOGIN');
    }
}
