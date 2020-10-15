<?php

declare(strict_types=1);

namespace Harpya\IP\VOs;

class InitialAuthResponseVO extends BaseVO
{
    // Application's endpoint which should receive this authorization from
    // Identity Provider
    protected $urlAuthorize;

    // client's browser IP
    protected $ipAddress;

    // Lifetime of this transaction
    protected $validUntil;

    // The caller Application have an endpoint, which expects receive
    // a token in the URL. This is used to validate the transaction
    protected $publicToken;

    // User's email
    protected $email;

    // Token which will end up as session ID of caller Application.
    protected $tokenRemoteSessionID;
}
