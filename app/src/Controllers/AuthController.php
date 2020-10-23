<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

use Harpya\IP\Constants as ConstantsIP;
use Harpya\IP\VOs\SignupVO;
use Harpya\IP\VOs\LoginVO;
use \Harpya\IP\VOs\InitialAuthRequestVO;
use \Harpya\IP\VOs\InitialAuthResponseVO;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\IP\Models\SessionEstablished;
use \Harpya\IP\Models\Application as ApplicationModel;
use \Harpya\IP\Services\ValidateInitialRequest;
use \Harpya\SDK\Utils;
use \Harpya\SDK\Constants;
use \Harpya\IP\Application;

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
        $this->response->setStatusCode($response['status_code'] ?? 200);

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

            $localAuthData = $this->session->get('auth_data');

            $remoteSessionID = Utils::generateRandomToken();

            $this->session->getAdapter()->markToRemoveSession(session_id());

            $this->session->getAdapter()->setSessionID(Utils::getSIDFromSessionToken($remoteSessionID)) ;
            // $this->session->start();
            $lifetime = 600;

            $this->cookies->set(
                session_name(),
                Utils::getSIDFromSessionToken($remoteSessionID),
                time() + $lifetime
            );
            $this->cookies->send();

            $userModel = $response['user'];

            $this->session->getAdapter()->userID = $userModel->id;
            $this->session->getAdapter()->ip = $_SERVER['REMOTE_ADDR'];

            // Create  a service with all these steps below:
            // (if configured) 2FA with Google Authenticator
            // (if configured) 2FA with Email
            // (if configured) 2FA with SMS
            // (if configured) Send welcome email
            // establish session, authorize it on caller App, and send back a redirection to User's Browser

            $sessionEstablishedModel = SessionEstablished::buildFromUserID($userModel->id);

            // 1. load token and request data
            $arrInitialRequest = $this->session->get('auth_request');
            $authRequestModel = null;
            if ($arrInitialRequest && isset($arrInitialRequest['id'])) {
                $authRequestModel = AuthRequest::findFirst(
                    $arrInitialRequest['id']
                );
            }

            // Initialize some variables
            $urlAuthorize = null;
            $urlRedirect = getenv(ConstantsIP::CONFIG_DEFAULT_URL_AFTER_LOGIN);
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

            $sessionEstablishedModel->token = $token;
            $sessionEstablishedModel->valid_until = time() + Utils::getTTL();
            $sessionEstablishedModel->remote_session_id = $remoteSessionID;

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

            $authData = $sessionEstablishedModel->jsonSerialize();

            $sid = $this->session->getId();
            $this->persistent->authData =
                        $authData;

            // redirect to requestor
            $this->response->setStatusCode(302);

            $this->response->setHeader(
                'Location',
                 $urlRedirect . $token
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
    public function authConfirm()
    {
        $tokenID = $this->request->get(Constants::KEY_TOKEN);

        $query = $this->modelsManager->createQuery(
        'SELECT * FROM \Harpya\IP\Models\SessionEstablished as sess, 
            \Harpya\IP\Models\User as usr WHERE
            usr.id = sess.user_id 
            AND sess.token = :token:'
        );

        // Execute the query returning a result if any
        $sessionCursor = $query->execute([
            'token' => $tokenID
        ]);
        $arrRegs = $sessionCursor->jsonSerialize();

        $userModel = null;
        $sessionEstablishedModel = null;
        $validationErrors = [];

        if (count($arrRegs) == 0) {
            $validationErrors[] = 'Session data not found';
        } else {
            $arrRecord = reset($arrRegs);

            $sessionEstablishedModel = $arrRecord['harpya\IP\Models\SessionEstablished'];
            $userModel = $arrRecord['harpya\IP\Models\User'];

            if (time() > $sessionEstablishedModel->valid_until) {
                $validationErrors[] = 'Token expired';
            }

            if ($sessionEstablishedModel->status !== 1) {
                $validationErrors[] = 'Session already started';
            }

            if ($sessionEstablishedModel->ip_address !== $this->request->get(Constants::KEY_CLIENT_IP)) {
                $validationErrors[] = 'Different IPs';
            }
        }

        if (!$sessionEstablishedModel && !$userModel) {
            $validationErrors[] = 'Invalid session data';
        }

        // $sessionEstablishedModel = SessionEstablished::findFirst([
        //     ' remote_session_id = :remote_session_id:  ',
        //     'bind' => [
        //         'remote_session_id' => $sessionID
        //     ]
        // ]);

        if (!empty($validationErrors)) {
            $this->response->setStatusCode(403);
            $this->response->setContent(json_encode([
                'success' => false,
                'msg' => 'Error during session validation',
                'reasons' => $validationErrors
            ]));
            $this->response->send();
            return ;
        }

        $sessionEstablishedModel->setStatus(2)->save();
        // $sessionEstablishedModel->status = 2;
        // $sessionEstablishedModel->updated_at = 'now()';
        // $sessionEstablishedModel->save();

        $this->response->setStatusCode(200);

        $response = ['email' => $userModel->email, 'session_id' => $sessionEstablishedModel->remote_session_id];
        // Constants::KEY_USER_EMAIL

        $this->response->setContent(json_encode($response));
        $this->response->send();

        return $response;
    }

    public function authRequest()
    {
        $response = [];

        $input[Constants::KEY_APPLICATION_ID] = $this->request->get(Constants::KEY_APPLICATION_ID);
        $input[Constants::KEY_APPLICATION_SECRET] = $this->request->get(Constants::KEY_APPLICATION_SECRET);

        $input[Constants::KEY_TOKEN] = $this->request->get(Constants::KEY_TOKEN);
        $input[Constants::KEY_CLIENT_IP] = $this->request->get(Constants::KEY_CLIENT_IP);
        $input[Constants::KEY_BASE_URL] = $this->request->get(Constants::KEY_BASE_URL);

        // Validate Application
        $application = ApplicationModel::findRegisteredApplication(
                                    $input[Constants::KEY_APPLICATION_ID],
                                    $input[Constants::KEY_APPLICATION_SECRET],
                                    $_SERVER['REMOTE_ADDR']
        );

        if (!$application) {
            http_response_code(403);
            $response['msg'] = 'Application not found';
            $response['success'] = false;

            $this->response->setStatusCode(302);
            // $this->response->setHeader(
            //     'Location',
            //      $urlRedirect
            // );

            $this->response->setContent(json_encode($response));
            $this->response->send();

            return $response;
        }

        $appArr = $application->jsonSerialize();

        $this->session->set('auth_data', $appArr);
        // return [$appArr['id']];

        // return ['x' => $application->jsonSerialize()];

        // print_r($application);
        // exit;

        // // Validate Origin IP
        // if ($input[Utils::KEY_CLIENT_IP] !== $_SERVER['REMOTE_ADDR']) {
        //     http_response_code(403);
        //     $response['msg'] = 'Origin does not match with initial request';
        //     $response['success'] = false;
        //     return $response;
        // }

        if ($input[Constants::KEY_TOKEN]) {
            // check if this token still active. Is a expired session in the client, and
            // may be possible still active in this I.P. instance. If so, will just return
            // a response to refresh the session on WebApp.
            // verify also if $input[Utils::KEY_CLIENT_IP] matches with record in
            // current I.P. DB.
        }

        // @TODO validate and authorize properly Application based on these data
        // $response[Utils::KEY_APPLICATION_ID] = $request->get(Utils::KEY_APPLICATION_ID);
        // $response[Utils::KEY_APPLICATION_SECRET] = $request->get(Utils::KEY_APPLICATION_SECRET);

        // Generate a new token

        // $input[Constants::KEY_TOKEN] = Utils::generateRandomToken();
        $tokenAuthRequest = Utils::generateRandomToken();

        $authRequest = new AuthRequest();

        $authRequest->app_id = $application->id;
        $authRequest->valid_until = time() + 600;
        $authRequest->token = $tokenAuthRequest;
        $authRequest->ip_address = $input[Constants::KEY_CLIENT_IP];
        $authRequest->url_authorize = $input[Constants::KEY_URL_AUTHORIZE] ?? $application->url_authorize;
        $authRequest->url_after_login = $input[Constants::KEY_URL_AFTER_LOGIN] ?? $application->url_after_login;
        $authRequest->save();

        $response[Constants::KEY_TOKEN] = $tokenAuthRequest;

        $response[Constants::KEY_CLIENT_IP] = $this->request->get(Constants::KEY_CLIENT_IP);
        // $response[Constants::KEY_CLIENT_IP . '_'] = $_SERVER['REMOTE_ADDR'];

        $response[Constants::KEY_ACTION] = \Harpya\SDK\IdentityProvider\Broker::ACTION_REDIRECT;

        // create token, store data on DB, and send back the token

        $response['authenticated'] = false;

        $this->response->setContent(json_encode($response));
        $this->response->send();
    }

    // -------------------------------
    // UTILITY METHODS
    // ---------------

    protected function getRegisteredApplication()
    {
        $application = ApplicationModel::findRegisteredApplication(
            $this->request->get(Constants::KEY_APPLICATION_ID),
            $this->request->get(Constants::KEY_APPLICATION_SECRET),
            $_SERVER['REMOTE_ADDR']
        );
        if ($application) {
            return $application;
        }
        $response = [];
        $this->response->setStatusCode(403);
        // http_response_code(403);
        $response['msg'] = 'Application not found';
        $response['success'] = false;

        $this->response->setStatusCode(302);

        $this->response->setContent(json_encode($response));
        $this->response->send();

        return $response;
    }

    /**
     * Utility method, to check if the CSRF token is valid.
     */
    protected function checkCsrfToken()
    {
        return $this->security->checkTokenOk($this);
    }
}
