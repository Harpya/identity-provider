<?php

declare(strict_types=1);

namespace Harpya\IP\Lib;

class Model extends \Phalcon\Mvc\Model
{
    protected function getDynField(string $field)
    {
        if (isset($this->$field)) {
            return $this->$field;
        } else {
            return '';
        }
    }
}
