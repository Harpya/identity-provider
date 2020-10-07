<?php

use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\IdentityProvider\Broker;
use \Harpya\SDK\Constants;
use \Harpya\IP\Models\Application;
use \Harpya\IP\Models\AuthRequest;

$router->addGet('/login/[:token]', function ($request = null, $parms = null, $di = null) {
    // $r = ['x' => $parms, 'r' => $_REQUEST];

    // print_r($r);
    // exit;

    $authRequest = AuthRequest::findFirst([
        'token = :token:',
        'bind' => ['token' => $parms[0]]
    ]);

    if (is_null($authRequest)) {
        http_response_code(404);
        return ['success' => false, 'msg' => 'Invalid request'];
    }

    if ($authRequest->ip_address !== $_SERVER['REMOTE_ADDR']) {
        http_response_code(403);
        return ['success' => false, 'msg' => 'Invalid origin request'];
    }

    if ($authRequest->valid_until < time()) {
        http_response_code(403);
        return ['success' => false, 'msg' => 'Auth request has expired'];
    }

    $authRequest->status = AuthRequest::STATUS_IN_PROGRESS;
    $authRequest->updated_at = 'now()';
    $authRequest->valid_until = time() + 600; // 10 minutes
    $authRequest->save();

    // $sess_test = $di->get('session')->get('auth_request');

    // session_start();
    $di->get('session')->set('auth_request', $authRequest->jsonSerialize());

    // $r['req'] = $authRequest->jsonSerialize();

    // return $r;
    $di->get('dispatcher')->forward([
        'controller' => 'index',
        'action' => 'index',
    ]);
    return Constants::RESPONSE_PROCEED_VIEW_PROCESSING;
});

$router->addPost('/api/v1/auth_request', function ($request = null, $parms = null, $di = null) {
    $response = [];

    $input[Constants::KEY_APPLICATION_ID] = $request->get(Constants::KEY_APPLICATION_ID);
    $input[Constants::KEY_APPLICATION_SECRET] = $request->get(Constants::KEY_APPLICATION_SECRET);

    $input[Constants::KEY_TOKEN] = $request->get(Constants::KEY_TOKEN);
    $input[Constants::KEY_CLIENT_IP] = $request->get(Constants::KEY_CLIENT_IP);
    $input[Constants::KEY_BASE_URL] = $request->get(Constants::KEY_BASE_URL);

    // Validate Application
    $application = Application::findRegisteredApplication(
                                $input[Constants::KEY_APPLICATION_ID],
                                $input[Constants::KEY_APPLICATION_SECRET],
                                $_SERVER['REMOTE_ADDR']
    );

    if (!$application) {
        http_response_code(403);
        $response['msg'] = 'Application not found';
        $response['success'] = false;
        return $response;
    }

    $appArr = $application->jsonSerialize();
    // return [$appArr['id']];

    // return ['x' => $application->jsonSerialize()];

    // print_r($application);
    // exit;

    // // Validate Origin IP
    // if ($input[Utils::KEY_CLIENT_IP] !== $_SERVER['REMOTE_ADDR']) {
    //     http_response_code(403);
    //     $response['msg'] = 'Origin does not match with initial request';
    //     $response['success'] = false;
    //     return $response;
    // }

    if ($input[Constants::KEY_TOKEN]) {
        // check if this token still active. Is a expired session in the client, and
        // may be possible still active in this I.P. instance. If so, will just return
        // a response to refresh the session on WebApp.
        // verify also if $input[Utils::KEY_CLIENT_IP] matches with record in
        // current I.P. DB.
    }

    // @TODO validate and authorize properly Application based on these data
    // $response[Utils::KEY_APPLICATION_ID] = $request->get(Utils::KEY_APPLICATION_ID);
    // $response[Utils::KEY_APPLICATION_SECRET] = $request->get(Utils::KEY_APPLICATION_SECRET);

    // Generate a new token

    $input[Constants::KEY_TOKEN] = Utils::generateRandomToken();

    $authRequest = new AuthRequest();

    $authRequest->app_id = $application->id;
    $authRequest->valid_until = time() + 600;
    $authRequest->token = $input[Constants::KEY_TOKEN];
    $authRequest->ip_address = $input[Constants::KEY_CLIENT_IP];
    $authRequest->url_authorize = $input[Constants::KEY_URL_AUTHORIZE] ?? $application->url_authorize;
    $authRequest->url_after_login = $input[Constants::KEY_URL_AFTER_LOGIN] ?? $application->url_after_login;
    $authRequest->save();

    $response[Constants::KEY_TOKEN] = $input[Constants::KEY_TOKEN];

    $response[Constants::KEY_CLIENT_IP] = $request->get(Constants::KEY_CLIENT_IP);
    $response[Constants::KEY_CLIENT_IP . '_'] = $_SERVER['REMOTE_ADDR'];

    $response[Constants::KEY_ACTION] = Broker::ACTION_REDIRECT;

    // create token, store data on DB, and send back the token

    $response['authenticated'] = false;

    // throw new \Exception('Not authenticated', 400);
    return $response;
});

$router->addGet('/api/v1/is_authenticated', function ($request = null, $parms = null, $di = null) {
    $response = [];

    $response['authenticated'] = false;

    throw new \Exception('Not authenticated', 400);
    return $response;
});

$router->addGet('/api/v1/authenticated', function ($request = null, $parms = null, $di = null) {
    $response = [];

    $response['authenticated'] = true;

    return $response;
});

// $router->addGet('/xpto/[:a]', function ($request = null, $parms = null, $di = null) {
//     if ($request->isPost()) {
//         $json = $request->getJsonRawBody(true);
//     } else {
//         $json = [];
//     }

//     $a = $parms[0] ?? '-';
//     $s = "Ok: $a " . $json['aaa'];

//     return $s;
// });

// $router->addGet('/phpinfo', function () {
//     phpinfo();
// });
