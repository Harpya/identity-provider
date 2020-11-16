<?php
declare(strict_types=1);

namespace Harpya\IP\Builders;

use \Harpya\IP\Models\User as UserModel;

class User
{
    public static function fromEmailPassword($email, $password) : UserModel
    {
        $user = new UserModel();
        $user->email = $email;
        $user->authentication_string = $password;
        return $user;
    }
}
