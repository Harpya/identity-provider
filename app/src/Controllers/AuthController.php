<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Harpya\IP\VOs\SignupVO;
use Harpya\IP\VOs\LoginVO;

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

                $response = \Harpya\IP\Services\Signup::execute(
                    SignupVO::factory(
                        SignupVO::class,
                    [
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'confirm_password' => $this->request->getPost('confirm_password'),
                        'accept_terms' => $this->request->getPost('accept_terms')
                    ]
                    )
                )->toArray();
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

            if ($response['success']) {
                $this->dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'index',
                    'params' => [
                        [
                            'error' => false,
                            'msg' => $response['msg'],
                            'status_code' => 200
                        ]
                    ]
                ]);
            } else {
                $this->dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'signup',
                    'params' => [
                        [
                            'error' => true,
                            'msg' => $response['msg'],
                            'status_code' => 400
                        ]
                    ]
                ]);
            }
        } else {
            $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }
    }

    public function loginAction()
    {
        if ($this->request->isPost()) {
            try {
                $this->checkCsrfToken();

                $response = \Harpya\IP\Services\Login::execute(
                    LoginVO::factory(
                        LoginVO::class,
                    [
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password')
                    ]
                    )
                )->toArray();

                // if everything ok, set session and proceed to requestor

                $user = $response['user'];
                if ($user) {
                    $token = $this->registerSession($user);
                    // redirect to requestor
                    $this->response->setContent(json_encode(['token' => $token]));
                    $this->response->setStatusCode(302);
                    $this->response->setHeader(
                        'Location',
                        \Harpya\IP\Application::getInstance()->getTargetURL()
                    );
                    return;
                }
            } catch (\Harpya\IP\Exceptions\CsrfTokenException $ex) {
                $this->dispatcher->forward([
                    'controller' => 'errors',
                    'action' => 'show500',
                ]);
                return ;
            }

            if ($response['success']) {
                $this->dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'index',
                    'params' => [
                        [
                            'error' => false,
                            'msg' => $response['msg'],
                            'status_code' => 200
                        ]
                    ]
                ]);
            } else {
                $this->dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'index',
                    'params' => [
                        [
                            'error' => true,
                            'msg' => $response['msg'],
                            'status_code' => 400
                        ]
                    ]
                ]);
            }
        } else {
            $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index',
            ]);
        }
    }

    // -------------------------------
    // UTILITY METHODS
    // ---------------

    /**
     * Utility method, to check if the CSRF token is valid.
     */
    protected function checkCsrfToken()
    {
        return $this->security->checkTokenOk($this);
    }

    /**
     *
     */
    protected function registerSession($user)
    {
        // create a valid token
        $token = hash('sha256', time() . \random_bytes(20));

        $this->session->set('auth', [
            'id' => $user->id,
            'email' => $user->email,
            'token' => $token
        ]);

        return $token;
    }
}
