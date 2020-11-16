<?php

use \Harpya\IP\Controllers\ForgotPasswordController;
use \Harpya\IP\Controllers\AuthController;
use \Harpya\SDK\Constants;
use \Harpya\IP\Models\Application;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\IdentityProvider\Broker;
use \Harpya\IP\Models\SessionEstablished;

$app = $this;

/**
 * Show the forgot password
 */
$app->get('/password/forgot', function () use ($app) {
    ForgotPasswordController::getInstance($app->getDI())->checkIfIsLogged()->showForgotPasswordForm();
});

/**
 * Create a token and send it by e-mail
 */
$app->post('/password/forgot', function () use ($app) {
    ForgotPasswordController::getInstance($app->getDI())->checkIfIsLogged()->processForgotPasswordRequest();
});

/**
 * Create a token and send it by e-mail
 */
$app->get('/password/forgot/{token}', function ($token) use ($app) {
    ForgotPasswordController::getInstance($app->getDI())->checkIfIsLogged()->proceedPasswordReset();
});
