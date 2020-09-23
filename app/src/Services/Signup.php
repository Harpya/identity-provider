<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

use \Harpya\IP\Models\User;
use \Harpya\IP\VOs\SignupVO;
use \Harpya\IP\VOs\ResponseVO;

class Signup
{
    public static function execute(SignupVO $data) : ResponseVO
    {
        $response = new ResponseVO();

        $email = $data->get('email');
        $password = $data->get('password');
        $confirmPassword = $data->get('confirm_password');
        $acceptedTerms = $data->get('accept_terms');

        if ('yes' !== $acceptedTerms) {
            $response->bind(['success' => false,  'msg' => 'It is necessary accept the terms']);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->bind(['success' => false,  'msg' => 'Invalid email format']);
        } elseif (strlen($password) < 6) {
            $response->bind(['success' => false,  'msg' => "Error creating $email: Small password"]);
        } elseif ($confirmPassword !== $password) {
            // throw new \Harpya\IP\Exceptions\ValidationException('Small password');
            $response->bind(['success' => false,  'msg' => "Error creating $email: password confirmation does not match"]);
        } else {
            $user = \Harpya\IP\Builders\User::fromEmailPassword($email, $password);
            $user->status = User::STATUS_ACTIVE;

            try {
                $user->save();

                $response->bind(['success' => true,  'msg' => "$email created with success  ", 'user_id' => $user->id]);
            } catch (\Exception $ex) {
                $response->bind(['success' => false,  'msg' => "Error creating $email: " . $ex->getMessage()]);
            }
        }
        return $response;
    }
}
