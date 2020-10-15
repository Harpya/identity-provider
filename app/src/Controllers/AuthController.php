<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Harpya\IP\VOs\SignupVO;
use Harpya\IP\VOs\LoginVO;
use \Harpya\IP\VOs\InitialAuthRequestVO;
use \Harpya\IP\VOs\InitialAuthResponseVO;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\IP\Models\SessionEstablished;
use \Harpya\IP\Services\ValidateInitialRequest;
use \Harpya\SDK\IdentityProvider\Utils;

class AuthController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Show the login page. If have $token informed, load it to session before proceed.
     */
    public function showPageLogin($token = false)
    {
        $response = [];

        if ($token) {
            // retrieve and validate initial request
            $token = $this->sanitizeToken($token);

            $initialRequestModel = AuthRequest::findFirst([
                'token = :token:',
                'bind' => ['token' => $token]
            ]);

            if ($initialRequestModel) {
                try {
                    $initialRequestVO = InitialAuthRequestVO::factory(InitialAuthRequestVO::class, [
                        'ipAddress' => $initialRequestModel->ip_address,
                        'validUntil' => $initialRequestModel->valid_until
                    ]);
                    $responseVO = ValidateInitialRequest::execute($initialRequestVO) ;
                    $response = $responseVO->toArray();

                    $response['error'] = !$response['success'];

                    if ($response['success']) {
                        $initialRequestModel->changeStatus(AuthRequest::STATUS_IN_PROGRESS);

                        $this->session->set('auth_request', $initialRequestModel->jsonSerialize());
                    }
                } catch (\Exception $ex) {
                    $response['error'] = true;
                    $response['msg'] = $ex->getMessage();
                    $response['status_code'] = 400;
                }
            } else {
                $response['error'] = true;
                $response['msg'] = "Token $token not found";
                $response['status_code'] = 404;
            }
        }
        $this->renderPageLogin($response);
    }

    /**
     *
     */
    protected function renderPageLogin(array $response = [])
    {
        $this->view->getRender(
            'auth',
            'login',
            $response
        );

        $this->response->setContent($this->view->getContent());
        $this->response->setStatusCode($response['status_code'] ?? 201);

        $this->response->send();
    }

    /**
     *
     */
    protected function sanitizeToken($token) : string
    {
        $finalToken = substr(
                htmlspecialchars($token, ENT_XHTML | ENT_QUOTES),
                0,
                100
        ); // sha256 string size
        return $finalToken;
    }

    public function doLogin()
    {
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

            // User not found
            if (!$response['user']) {
                $this->renderPageLogin(['error' => true, 'msg' => 'User not found OR invalid credential']);
                return;
            }

            $userModel = $response['user'];

            $sessionEstablishedModel = SessionEstablished::buildFromUserID($userModel->id);

            // 1. load token and request data
            $arrInitialRequest = $this->session->get('auth_request');

            $authRequestModel = AuthRequest::findFirst(
                $arrInitialRequest['id']
            );

            // Initialize some variables
            $urlAuthorize = null;
            $urlRedirect = getenv('CONFIG_DEFAULT_URL_AFTER_LOGIN');
            $sessionEstablishedModel->app_id = 0;

            // If $authRequestModel is NOT null, then change its stats and
            // update the record on DB. Also get the URLs values from
            if (!is_null($authRequestModel)) {
                $authRequestModel->status = AuthRequest::STATUS_FINISHED;
                $authRequestModel->save();

                $urlAuthorize = $authRequestModel->url_authorize;
                $urlRedirect = $authRequestModel->url_after_login;
                $sessionEstablishedModel->app_id = $authRequestModel->app_id;
            }

            Utils::addSlashAtEnd($urlRedirect);

            // 2. Store user data in session
            // $_SESSION['user'] = $user;

            // 3. Create token
            $token = Utils::generateRandomToken();
            $remoteSessionID = Utils::generateRandomToken();

            $sessionEstablishedModel->token = $token;
            $sessionEstablishedModel->valid_until = time() + Utils::getTTL();

            // $remoteSessionID = hash('sha256', time() . \random_bytes(20));

            // 4. make a call to application to authorize this user
            if ($urlAuthorize) {
                $authResponse = InitialAuthResponseVO::factory(
                    null,
                    [
                        'urlAuthorize' => $urlAuthorize,
                        'ipAddress' => $sessionEstablishedModel->ip_address,
                        'validUntil' => $sessionEstablishedModel->valid_until,
                        'publicToken' => $sessionEstablishedModel->token,
                        'email' => $userModel->email,
                        'tokenRemoteSessionID' => $remoteSessionID
                    ]
                );

                $appResponseVO = \Harpya\IP\Services\AuthorizeAppLogin::execute($authResponse);

                if (!$appResponseVO->get('success')) {
                    $response['error'] = true;
                    $response['msg'] = $appResponseVO->get('msg');
                    $response['status_code'] = 400;

                    $this->renderPageLogin($response);
                    return;
                }
                // $client = new \GuzzleHttp\Client();
                // try {
                //     $appReturn = $client->request('POST', $urlAuthorize, [
                //         'form_params' => [
                //             'token' => $token,
                //             'client_ip' => $_SERVER['REMOTE_ADDR'],
                //             'email' => $user->email,
                //             'session_id' => $remoteSessionID
                //         ]
                //     ]);
                // } catch (\Exception $e) {
                //     echo '<pre>';
                //     echo $e->getMessage();
                //     echo "\n " . $e->getTraceAsString();
                //     exit;
                // }

                // $appReturnContents = $appReturn->getBody()->getContents();
            }
            $sessionEstablishedModel->save();

            $this->session->getAdapter()->userID = $user->id;
            $this->session->getAdapter()->ip = $_SERVER['REMOTE_ADDR'];

            $this->session->set(
                        'auth_data',
                        $sessionEstablishedModel->jsonSerialize()
                    );

            // redirect to requestor
            $this->response->setStatusCode(302);

            $this->response->setHeader(
                'Location',
                 $urlRedirect . $remoteSessionID
            );
            $this->response->send();
            return;
        } catch (\Harpya\IP\Exceptions\CsrfTokenException $ex) {
            $this->dispatcher->forward([
                'controller' => 'errors',
                'action' => 'show500',
            ]);
            return ;
        }
    }

    /**
     *
     */
    public function doLogout()
    {
        $this->session->remove('auth_data');
        $this->showPageLogin();
    }

    /**
     *
     */
    // public function signupAction___(): void
    // {
    //     if ($this->request->isPost()) {
    //         try {
    //             $this->checkCsrfToken();

    //             $response = \Harpya\IP\Services\Signup::execute(
    //                 SignupVO::factory(
    //                     SignupVO::class,
    //                 [
    //                     'email' => $this->request->getPost('email'),
    //                     'password' => $this->request->getPost('password'),
    //                     'confirm_password' => $this->request->getPost('confirm_password'),
    //                     'accept_terms' => $this->request->getPost('accept_terms')
    //                 ]
    //                 )
    //             )->toArray();
    //         } catch (\Harpya\IP\Exceptions\CsrfTokenException $ex) {
    //             $this->dispatcher->forward([
    //                 'controller' => 'errors',
    //                 'action' => 'show500',
    //             ]);
    //             return ;
    //         } catch (\Harpya\IP\Exceptions\ValidationException $ex) {
    //             $this->dispatcher->forward([
    //                 'controller' => 'index',
    //                 'action' => 'signup',
    //                 'params' => [
    //                     [
    //                         'error' => true,
    //                         'msg' => $ex->getMessage(),
    //                         'status_code' => 400
    //                     ]
    //                 ]
    //             ]);
    //             return ;
    //         }

    //         if ($response['success']) {
    //             $this->dispatcher->forward([
    //                 'controller' => 'index',
    //                 'action' => 'index',
    //                 'params' => [
    //                     [
    //                         'error' => false,
    //                         'msg' => $response['msg'],
    //                         'status_code' => 200
    //                     ]
    //                 ]
    //             ]);
    //         } else {
    //             $this->dispatcher->forward([
    //                 'controller' => 'index',
    //                 'action' => 'signup',
    //                 'params' => [
    //                     [
    //                         'error' => true,
    //                         'msg' => $response['msg'],
    //                         'status_code' => 400
    //                     ]
    //                 ]
    //             ]);
    //         }
    //     } else {
    //         $this->dispatcher->forward([
    //             'controller' => 'index',
    //             'action' => 'index',
    //         ]);
    //     }
    // }

    /**
     *
     */
    // public function loginAction()
    // {
    //     if ($this->request->isPost()) {
    //         try {
    //             $this->checkCsrfToken();

    //             $response = \Harpya\IP\Services\Login::execute(
    //                 LoginVO::factory(
    //                     LoginVO::class,
    //                 [
    //                     'email' => $this->request->getPost('email'),
    //                     'password' => $this->request->getPost('password')
    //                 ]
    //                 )
    //             )->toArray();

    //             // if everything ok, set session and proceed to requestor

    //             $user = $response['user'];
    //             if ($user) {
    //                 $sessionEstablished = new SessionEstablished();
    //                 $sessionEstablished->user_id = $user->id;
    //                 $sessionEstablished->ip_address = $_SERVER['REMOTE_ADDR'];

    //                 // 1. load token and request data
    //                 $initialRequest = $this->session->get('auth_request');

    //                 $authRequest = AuthRequest::findFirst(
    //                     $initialRequest['id']
    //                 );

    //                 if (is_null($authRequest)) {
    //                     $urlAuthorize = null;
    //                     $urlRedirect = getenv('CONFIG_DEFAULT_URL_AFTER_LOGIN');
    //                     $sessionEstablished->app_id = 0;
    //                 } else {
    //                     $authRequest->status = AuthRequest::STATUS_FINISHED;
    //                     $authRequest->save();

    //                     $urlAuthorize = $authRequest->url_authorize;
    //                     $urlRedirect = $authRequest->url_after_login;
    //                     $sessionEstablished->app_id = $authRequest->app_id;
    //                 }

    //                 Utils::addSlashAtEnd($urlRedirect);

    //                 // 2. Store user data in session
    //                 // $_SESSION['user'] = $user;

    //                 // 3. Create token
    //                 $token = Utils::generateRandomToken();
    //                 $remoteSessionID = Utils::generateRandomToken();

    //                 $sessionEstablished->token = $token;

    //                 $sessionEstablished->valid_until = time() + Utils::getTTL();

    //                 // $remoteSessionID = hash('sha256', time() . \random_bytes(20));

    //                 // 4. make a call to application to authorize this user
    //                 if ($urlAuthorize) {
    //                     $client = new \GuzzleHttp\Client();
    //                     try {
    //                         $appReturn = $client->request('POST', $urlAuthorize, [
    //                             'form_params' => [
    //                                 'token' => $token,
    //                                 'client_ip' => $_SERVER['REMOTE_ADDR'],
    //                                 'email' => $user->email,
    //                                 'session_id' => $remoteSessionID
    //                             ]
    //                         ]);
    //                     } catch (\Exception $e) {
    //                         echo '<pre>';
    //                         echo $e->getMessage();
    //                         echo "\n " . $e->getTraceAsString();
    //                         exit;
    //                     }

    //                     $appReturnContents = $appReturn->getBody()->getContents();
    //                 }
    //                 $sessionEstablished->save();

    //                 $this->session->getAdapter()->userID = $user->id;
    //                 $this->session->getAdapter()->ip = $_SERVER['REMOTE_ADDR'];

    //                 $this->session->set(
    //                             'auth_data',
    //                             $sessionEstablished->jsonSerialize()
    //                         );

    //                 // redirect to requestor
    //                 $this->response->setStatusCode(302);

    //                 $this->response->setHeader(
    //                     'Location',
    //                      $urlRedirect . $remoteSessionID
    //                 );
    //                 return;
    //             }
    //         } catch (\Harpya\IP\Exceptions\CsrfTokenException $ex) {
    //             $this->dispatcher->forward([
    //                 'controller' => 'errors',
    //                 'action' => 'show500',
    //             ]);
    //             return ;
    //         }

    //         if ($response['success']) {
    //             $this->dispatcher->forward([
    //                 'controller' => 'index',
    //                 'action' => 'index',
    //                 'params' => [
    //                     [
    //                         'error' => false,
    //                         'msg' => $response['msg'],
    //                         'status_code' => 200
    //                     ]
    //                 ]
    //             ]);
    //         } else {
    //             $this->dispatcher->forward([
    //                 'controller' => 'index',
    //                 'action' => 'index',
    //                 'params' => [
    //                     [
    //                         'error' => true,
    //                         'msg' => $response['msg'],
    //                         'status_code' => 400
    //                     ]
    //                 ]
    //             ]);
    //         }
    //     } else {
    //         // $this->dispatcher->forward([
    //         //     'controller' => 'index',
    //         //     'action' => 'index',
    //         // ]);
    //     }
    // }

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
