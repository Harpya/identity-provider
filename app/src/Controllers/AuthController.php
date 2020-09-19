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

class AuthController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        $this->tag->setTitle('Welcome');
    }

    public function signupAction(): void
    {
        if ($this->request->isPost()) {
            if (!$this->security->checkTokenOk($this)) {
                $this->dispatcher->forward([
                    'controller' => 'errors',
                    'action' => 'show500',
                ]);
                return ;
            }
        }
        print_r($_REQUEST);
        exit;
    }
}
