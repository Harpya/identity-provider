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

class IndexController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        $this->tag->setTitle('Welcome');
    }

    public function indexAction(): void
    {
        $this->setupCsrfToken();
    }
}
