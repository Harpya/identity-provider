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

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    protected function initialize()
    {
        //
    }

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
