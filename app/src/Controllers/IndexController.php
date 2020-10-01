<?php
declare(strict_types=1);

/**
 * This file is part of the Invo.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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

        // $pack = base64_encode(json_encode([
        //     'authorize' => 'http://localhost:1991/authorize',
        //     'application_id' => 'A0D47F',
        //     'application_token' => 'abcdef0123456789',
        //     'client_ip' => $_SERVER['REMOTE_ADDR']
        // ]));

        // 2. Validate data
        //      application_id with application_token
        //      client_ip should match
        //      'authorize' URL should match with condigured whitelist
        Application::validateAuthRequestToken($request);

        // 3. Store these data in session
        $this->session->set('request', $request);
        //$_SESSION['request'] = $request;

        $x = $this->session->get('request');

        $count++;
        // 4. Show the login page

        // echo 'Ok... <pre>';
        // echo "\n $t \n";
        // print_r($this->request->getHeaders());
        // exit;
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
