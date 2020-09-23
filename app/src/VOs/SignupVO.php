<?php

declare(strict_types=1);

namespace Harpya\IP\VOs;

class SignupVO extends BaseVO
{
    protected $email;
    protected $password;
    protected $confirm_password;
    protected $accept_terms;
}
