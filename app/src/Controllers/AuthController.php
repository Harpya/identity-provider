<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Harpya\IP\Services\AuthService;

class AuthController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     *
     */
    public function signupAction(): void
    {
        if ($this->request->isPost()) {
            try {
                $this->checkCsrfToken();
                $response = AuthService::getInstance()->execSignup($this);
            } catch (\Harpya\IP\Exceptions\CsrfTokenException $ex) {
                $this->dispatcher->forward([
                    'controller' => 'errors',
                    'action' => 'show500',
                ]);
                return ;
            } catch (\Harpya\IP\Exceptions\ValidationException $ex) {
                $this->dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'signup',
                    'params' => [
                        [
                            'error' => true,
                            'msg' => $ex->getMessage(),
                            'status_code' => 400
                        ]
                    ]
                ]);
                return ;
            }
            if (is_null($response)) {
                $response = '';
            }
            $this->response->setContent($response);
        } else {
            $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
            return ;
        }
    }

    protected function checkCsrfToken()
    {
        return $this->security->checkTokenOk($this);
    }
}
