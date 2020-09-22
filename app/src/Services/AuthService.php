<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

use \Harpya\IP\Models\User;

class AuthService extends BaseService
{
    public function execSignup($controller)
    {
        // Validate data

        $email = $controller->request->get('email');
        $password = $controller->request->get('password');
        $confirmPassword = $controller->request->get('confirm_password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Harpya\IP\Exceptions\ValidationException('Invalid email format');
        }

        if (strlen($password) < 6) {
            // throw new \Harpya\IP\Exceptions\ValidationException('Small password');
            return $response = ['success' => false,  'msg' => "Error creating $email: Small password"];
        }

        if ($confirmPassword !== $password) {
            // throw new \Harpya\IP\Exceptions\ValidationException('Small password');
            return $response = ['success' => false,  'msg' => "Error creating $email: password confirmation does not match"];
        }

        $user = new User();
        $user->email = $controller->request->get('email');
        $user->status = User::STATUS_ACTIVE;

        // @TODO: apply a 2nd layer of hashing here
        $user->authentication_string = $controller->request->get('password');

        try {
            $user->save();

            $response = ['success' => true,  'msg' => "$email created with success  ", 'user_id' => $user->id];
        } catch (\Exception $ex) {
            $response = ['success' => false,  'msg' => "Error creating $email: " . $ex->getMessage()];
        }

        return $response;
    }
}
