<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Phalcon\Mvc\Controller;

/**
 *
 */
class BaseController extends Controller
{
    public static $instance = [];

    /**
     *
     */
    public static function getInstance($di = null)
    {
        $className = static::class;
        if (!static::$instance[$className]) {
            static::$instance[$className] = new $className();
            if ($di) {
                static::$instance[$className]->setDI($di);
            }
        }
        return static::$instance[$className];
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->tag->setTitle('Login');
        $this->view->setVar('frmClasses', ['login' => 'normal']);
        $this->view->setVar('notice', '');
        $this->view->setVar('link_terms', '<a href="#">terms</a>');
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

    /**
     *
     */
    protected function prepareFeedback($parms) : void
    {
        if (!$parms) {
            return;
        }
        if (isset($parms['error']) && $parms['error']) {
            $this->view->setVar('error', true);
        } else {
            $this->view->setVar('error', false);
        }

        if (isset($parms['msg'])) {
            $this->view->setVar('msg', $parms['msg']);
        }

        if (isset($parms['status_code'])) {
            $this->response->setStatusCode($parms['status_code']);
        }
    }

    /**
    * Utility method, to check if the CSRF token is valid.
    */
    protected function checkCsrfToken()
    {
        return $this->security->checkTokenOk($this);
    }
}
