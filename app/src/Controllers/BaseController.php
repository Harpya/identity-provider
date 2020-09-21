<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Phalcon\Mvc\Controller;

/**
 *
 */
class BaseController extends Controller
{
    /**
     *
     */
    protected function initialize()
    {
        $this->tag->setTitle('Login');
        $this->view->setVar('frmClasses', ['login' => 'normal']);
        $this->view->setVar('notice', '');
    }

    /**
     *
     */
    protected function setupCsrfToken()
    {
        $csrfKey = $this->security->getTokenKey();
        $csrfValue = $this->security->getToken();

        $this->session->set('auth', [
            $csrfKey,
            $csrfValue
        ]);

        $this->view->csrfKey = $csrfKey;
        $this->view->csrfValue = $csrfValue;
    }
}
