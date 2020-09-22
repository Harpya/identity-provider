<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

class BaseService
{
    protected static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            $className = static::class;
            static::$instance = new $className();
        }
        return static::$instance;
    }
}
