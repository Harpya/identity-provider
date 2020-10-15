<?php

use \Harpya\IP\Controllers\IdentityController;
use \Harpya\IP\Controllers\AuthController;

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
