<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

use \Harpya\IP\Models\User;
use \Harpya\IP\VOs\LoginVO;
use \Harpya\IP\VOs\ResponseLoginVO;

class Login
{
    public static function execute(LoginVO $data) : ResponseLoginVO
    {
        $response = new ResponseLoginVO();

        $email = $data->get('email');
        $password = $data->get('password');

        if (empty($email)) {
            $response->bind(['success' => false,  'msg' => 'Email not informed']);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->bind(['success' => false,  'msg' => 'Invalid email format']);
        } elseif (empty($password)) {
            $response->bind(['success' => false,  'msg' => 'Password not informed']);
        } else {
            $user = User::authenticate($email, $password);

            if ($user) {
                if ($user->status === User::STATUS_ACTIVE) {
                    $response->bind(['success' => true, 'user' => $user]);
                } else {
                    $response->bind(['success' => false,  'msg' => "User $email is inactive"]);
                }
            } else {
                $response->bind(['success' => false,  'msg' => 'Invalid email or password']);
            }
        }
        return $response;
    }
}
