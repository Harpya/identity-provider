<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Phalcon\Mvc\View;
use \Harpya\IP\Application;

class IndexController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Workaround to not add "Action" in the method.
     */
    public function __call($name, $parms)
    {
        if ('Action' === substr($name, -6)) {
            $expectedMethod = substr($name, 0, -6);
            $resp = \call_user_func_array([$this, $expectedMethod], $parms);
        }

        if ($resp) {
            if (\is_scalar($resp)) {
                $this->response->setContent($resp);
            }
        }
    }

    /**
     *
     */
    public function indexAction($parms = null)
    {
        if ($this->request->has('t')) {
            $this->storeRedirectionParams($this->request->get('t'));
        }

        $this->setupCsrfToken();
        $this->prepareFeedback($parms);
    }

    protected function storeRedirectionParams($t)
    {
        // 1. Decode the $t package
        $request = Application::decodeAuthRequestToken($t);

        // 2. Validate data
        //      application_id with application_token
        //      client_ip should match
        //      'authorize' URL should match with condigured whitelist
        Application::validateAuthRequestToken($request);

        // 3. Store these data in session
        $this->session->set('request', $request);
    }

    /**
     *
     */
    public function signupAction($parms = null)
    {
        $this->view->setVar('frmClasses', ['signup' => 'normal']);
        $this->setupCsrfToken();
        $this->prepareFeedback($parms);
    }
}
