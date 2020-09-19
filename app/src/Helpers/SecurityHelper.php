<?php
declare(strict_types=1);

namespace Harpya\IP\Helpers;

use Phalcon\Security;

class SecurityHelper extends Security
{
    public function checkTokenOk($controller)
    {
        $csrfKey = $controller->session->get('auth')[0];
        $csrfValue = $controller->session->get('auth')[1];

        if ($controller->request->get($csrfKey) === $csrfValue) {
            return true;
        } else {
            return false;
        }
    }
}
