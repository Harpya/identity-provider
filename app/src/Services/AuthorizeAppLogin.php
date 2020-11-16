<?php
declare(strict_types=1);

namespace Harpya\IP\Services;

use \Harpya\IP\Models\User;
use \Harpya\IP\VOs\InitialAuthResponseVO;
use \Harpya\IP\VOs\ResponseVO;

class AuthorizeAppLogin
{
    public static function execute(InitialAuthResponseVO $authResponseVO) : ResponseVO
    {
        $response = new ResponseVO();

        $client = new \GuzzleHttp\Client();
        try {
            $appReturn = $client->request('POST', $authResponseVO->get('urlAuthorize'), [
                'form_params' => [
                    'token' => $authResponseVO->get('publicToken'),
                    'client_ip' => $authResponseVO->get('ipAddress'),
                    'email' => $authResponseVO->get('email'),
                    'session_id' => $authResponseVO->get('tokenRemoteSessionID'),
                ]
            ]);
            $response = new ResponseVO(['success' => true]);
        } catch (\Exception $ex) {
            $response = new ResponseVO(['success' => false, 'msg' => $ex->getMessage()]);
        }

        return $response;
    }
}
