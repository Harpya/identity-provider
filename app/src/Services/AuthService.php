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
        $acceptedTerms = $controller->request->get('accept_terms');

        if ('yes' !== $acceptedTerms) {
            return ['success' => false,  'msg' => 'It is necessary accept the terms'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Harpya\IP\Exceptions\ValidationException('Invalid email format');
        }

        if (strlen($password) < 6) {
            // throw new \Harpya\IP\Exceptions\ValidationException('Small password');
            return  ['success' => false,  'msg' => "Error creating $email: Small password"];
        }

        if ($confirmPassword !== $password) {
            // throw new \Harpya\IP\Exceptions\ValidationException('Small password');
            return  ['success' => false,  'msg' => "Error creating $email: password confirmation does not match"];
        }

        $user = $this->getUser($controller->request->get('email'), $controller->request->get('password'));
        $user->status = User::STATUS_ACTIVE;

        try {
            $user->save();

            $response = ['success' => true,  'msg' => "$email created with success  ", 'user_id' => $user->id];
        } catch (\Exception $ex) {
            $response = ['success' => false,  'msg' => "Error creating $email: " . $ex->getMessage()];
        }

        return $response;
    }

    public function execLogin($controller)
    {
        $email = $controller->request->get('email');
        $password = $controller->request->get('password');

        if (empty($email)) {
            return  ['success' => false,  'msg' => 'Email not informed'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Harpya\IP\Exceptions\ValidationException('Invalid email format');
        }

        if (empty($password)) {
            return  ['success' => false,  'msg' => 'Password not informed'];
        }

        $user = User::authenticate($controller->request->get('email'), $controller->request->get('password'));

        if (!$user) {
            return  ['success' => false,  'msg' => 'Invalid email or password'];
        }
    }

    /**
     *
     */
    protected function getUser($email = null, $password = null) : User
    {
        $user = new User();
        $user->email = $email;
        $user->authentication_string = $password;
        return $user;
    }
}
