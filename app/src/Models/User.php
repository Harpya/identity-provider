<?php

declare(strict_types=1);

namespace Harpya\IP\Models;

use Phalcon\Mvc\Model;

class User extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_VERIFICATION_PENDING = 1;
    const STATUS_ACTIVE = 2;

    public function initialize()
    {
        $this->setSource('users');
        $this->hasMany('id', 'SessionEstablished', 'user_id');
    }

    public static function authenticate($email, $password)
    {
        $resp = static::findFirst([
            'email = :email: and authentication_string = :password:',
            'bind' => ['email' => $email, 'password' => $password]
        ]);
        return $resp;
    }

    public function checkIfExists()
    {
        $userModel = static::findFirst([
            'email = :email: ',
            'bind' => ['email' => $this->email]
        ]);

        if ($userModel) {
            if ($userModel->status == self::STATUS_INACTIVE) {
                throw new \Exception("User {$this->email} already exists, but is inactive.");
            }
            throw new \Exception("User {$this->email} already exists.");
        }
    }
}
