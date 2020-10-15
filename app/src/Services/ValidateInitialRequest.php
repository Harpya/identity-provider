<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

use \Harpya\IP\Models\User;
use \Harpya\IP\VOs\SignupVO;
use \Harpya\IP\VOs\ResponseVO;
use \Harpya\IP\VOs\InitialAuthRequestVO;

class ValidateInitialRequest
{
    public static function execute(InitialAuthRequestVO $authRequest) : ResponseVO
    {
        $response = new ResponseVO(['success' => true]);

        if ($authRequest->get('ipAddress') !== $_SERVER['REMOTE_ADDR']) {
            return  new ResponseVO(['success' => false, 'msg' => 'Invalid origin request']);
        }

        if ($authRequest->get('validUntil') < time()) {
            return new ResponseVO(['success' => false, 'msg' => 'Auth request has expired']);
        }

        return $response;
    }
}
