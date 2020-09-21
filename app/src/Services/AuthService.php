<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

class AuthService extends BaseService
{
    public function execSignup($controller)
    {
        // Validate data

        $email = $controller->request->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Harpya\IP\Exceptions\ValidationException('Invalid email format');
        }

        if (strlen($email) < 10) {
            throw new \Harpya\IP\Exceptions\ValidationException('Debug - small email');
        }

        $response = $controller->request->get('email');
        return $response;
    }
}
