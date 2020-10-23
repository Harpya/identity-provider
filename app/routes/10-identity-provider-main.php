<?php

use \Harpya\IP\Controllers\IdentityController;
use \Harpya\IP\Controllers\AuthController;
use \Harpya\SDK\Constants;
use \Harpya\IP\Models\Application;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\IdentityProvider\Broker;
use \Harpya\IP\Models\SessionEstablished;

$app = $this;

/**
 *
 */
$app->get('/identity/signup', function () use ($app) {
    IdentityController::getInstance($app->getDI())->preSignup();
});

/**
 *
 */
$app->post('/identity/signup', function () use ($app) {
    IdentityController::getInstance($app->getDI())->doSignup();
});

/**
 * Browser redirected by caller-application, which comes
 * with token previously defined.
 */
$app->get('/auth/login/{token}', function ($token) use ($app) {
    AuthController::getInstance($app->getDI())->showPageLogin($token);
});

$app->get('/auth/login', function () use ($app) {
    AuthController::getInstance($app->getDI())->showPageLogin();
});

$app->post('/auth/login', function () use ($app) {
    AuthController::getInstance($app->getDI())->doLogin();
});

$app->get('/profile', function () use ($app) {
    IdentityController::getInstance($app->getDI())->showProfile();
});

$app->post('/api/v1/auth_confirm', function () use ($app) {
    AuthController::getInstance($app->getDI())->authConfirm();
});

$app->post('/api/v1/auth_request', function () use ($app) {
    AuthController::getInstance($app->getDI())->authRequest();
    return;
});

$app->notFound(
    function () use ($app) {
        $urlRedirect = getenv(Constants::CONFIG_HIP_HOSTNAME) . getenv(Constants::CONFIG_HIP_DEFAULT_URL);
        $app->response->setStatusCode(302);
        $app->response->setHeader(
            'Location',
             $urlRedirect
        );
        $app->response->send();
    }
);
