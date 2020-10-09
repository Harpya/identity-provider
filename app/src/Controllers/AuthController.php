<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Harpya\IP\VOs\SignupVO;
use Harpya\IP\VOs\LoginVO;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\IP\Models\SessionEstablished;
use \Harpya\SDK\IdentityProvider\Utils;

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

    /**
     *
     */
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
                    $sessionEstablished = new SessionEstablished();
                    $sessionEstablished->user_id = $user->id;
                    $sessionEstablished->ip_address = $_SERVER['REMOTE_ADDR'];

                    // 1. load token and request data
                    $initialRequest = $this->session->get('auth_request');

                    $authRequest = AuthRequest::findFirst(
                        $initialRequest['id']
                    );

                    if (is_null($authRequest)) {
                        $urlAuthorize = null;
                        $urlRedirect = getenv('CONFIG_DEFAULT_URL_AFTER_LOGIN');
                        $sessionEstablished->app_id = 0;
                    } else {
                        $authRequest->status = AuthRequest::STATUS_FINISHED;
                        $authRequest->save();

                        $urlAuthorize = $authRequest->url_authorize;
                        $urlRedirect = $authRequest->url_after_login;
                        $sessionEstablished->app_id = $authRequest->app_id;
                    }

                    if (!$urlRedirect || (is_string($urlRedirect) && substr($urlRedirect, -1) !== '/')) {
                        $urlRedirect .= '/';
                    }

                    // 2. Store user data in session
                    // $_SESSION['user'] = $user;

                    // 3. Create token
                    $token = Utils::generateRandomToken();
                    $remoteSessionID = Utils::generateRandomToken();

                    $sessionEstablished->token = $token;

                    $ttl = getenv('CONFIG_SESSION_TTL');
                    if (!$ttl) {
                        $ttl = (60 * 60 * 24); // 1 day
                    }

                    $sessionEstablished->valid_until = time() + $ttl;

                    // $remoteSessionID = hash('sha256', time() . \random_bytes(20));

                    // 4. make a call to application to authorize this user
                    if ($urlAuthorize) {
                        $client = new \GuzzleHttp\Client();
                        try {
                            $appReturn = $client->request('POST', $urlAuthorize, [
                                'form_params' => [
                                    'token' => $token,
                                    'client_ip' => $_SERVER['REMOTE_ADDR'],
                                    'email' => $user->email,
                                    'session_id' => $remoteSessionID
                                ]
                            ]);
                        } catch (\Exception $e) {
                            echo '<pre>';
                            echo $e->getMessage();
                            echo "\n " . $e->getTraceAsString();
                            exit;
                        }

                        $appReturnContents = $appReturn->getBody()->getContents();
                    }
                    $sessionEstablished->save();

                    $this->session->set(
                                'auth_data',
                                $sessionEstablished->jsonSerialize()
                            );

                    // redirect to requestor
                    $this->response->setStatusCode(302);

                    $this->response->setHeader(
                        'Location',
                         $urlRedirect . $remoteSessionID
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
}
