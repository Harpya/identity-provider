<?php

declare(strict_types=1);

namespace Harpya\IP\VOs;

class BaseVO
{
    /**
     * Builds an instance of $className, and bind the values
     */
    public static function factory($className, ?array $values = [])
    {
        if (is_null($className)) {
            $className = static::class;
        }

        $obj = new $className();

        if (!is_a($obj, BaseVO::class)) {
            throw new \Exception("Invalid class: $className. Expected be an inherited class from BaseVO");
        }

        if (is_array($values) && !empty($values)) {
            $obj->bind($values);
        }

        return $obj;
    }

    public function __construct($valuesToBind = [])
    {
        $this->bind($valuesToBind);
    }

    /**
     *
     */
    public function bind(?array $values)
    {
        $lsFields = \get_class_vars(static::class);
        foreach ($lsFields as $fieldName => $value) {
            if (isset($values[$fieldName])) {
                $this->$fieldName = $values[$fieldName];
            }
        }
    }

    /**
     *
     */
    public function toArray():array
    {
        return \get_object_vars($this);
    }

    /**
     *
     */
    public function get($key)
    {
        if (\property_exists(static::class, $key)) {
            return $this->$key;
        }
    }
}
