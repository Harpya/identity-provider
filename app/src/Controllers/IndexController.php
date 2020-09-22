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
        $this->setupCsrfToken();
        $this->prepareFeedback($parms);
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