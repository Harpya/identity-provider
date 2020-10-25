<?php
declare(strict_types=1);

namespace Harpya\IP\Controllers;

// error_reporting(E_ALL);

use \Phalcon\Mvc\View;
use \Harpya\SDK\Constants;
use Harpya\IP\VOs\SignupVO;
use Harpya\IP\VOs\LoginVO;
use \Harpya\IP\Models\AuthRequest;
use \Harpya\IP\Models\SessionEstablished;
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\IP\Models\User;

class IdentityController extends BaseController
{
    /**
     * Display the signup page
     */
    public function preSignup()
    {
        $this->setupCsrfToken();

        $this->view->getRender(
            'identity',
            'preSignup',
            []
        );

        $this->response->setContent($this->view->getContent());
        $this->response->setStatusCode(200);

        $this->response->send();
    }

    /**
     * Performs user signup
     */
    public function doSignup()
    {
        $response = [];
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
            $response['error'] = true;
            $response['msg'] = $ex->getMessage();
            $response['status_code'] = 500;
        } catch (\Harpya\IP\Exceptions\ValidationException $ex) {
            $response['error'] = true;
            $response['msg'] = $ex->getMessage();
            $response['status_code'] = 400;
        }

        $this->view->getRender(
            'identity',
            'postSignup',
            $response
        );

        $this->response->setContent($this->view->getContent());
        $this->response->setStatusCode($response['status_code'] ?? 201);

        $this->response->send();
    }

    public function showProfile()
    {
        $userData = [];
        $authData = $this->session->get('auth_data');
        $userID = $authData['user_id'] ?? false;

        if ($userID) {
            $user = User::findFirst([
                'id = :id:',
                'bind' => ['id' => $userID]
            ]);

            // $this->session->start();
            // echo '<pre>';
            $userData = $user->jsonSerialize();
        } else {
            // go to login
            $this->response->setStatusCode(302);

            $this->response->setHeader('Location', '/');
            $this->response->send();
        }

        // $response = [];

        // $sessionData = $this->session->get('auth_data');

        // $response['session'] = $sessionData;
        // $response['email'] = $this->persistent->authData['user_id'];
        // print_r($sessionData);

        // $userData['_'] = $userData ;

        $data = [
            'user' => $user,
            'now' => date('Y-m-d H:i:s'),
            'dateTimeLastLogin' => (new \Harpya\IP\BOs\User($user->email))->load()->getInfoLastLogin()->get('dateTime'),
            'user_dyn_attributes' => [
                [
                    'name' => 'Name',
                    'value' => 'Ed'
                ],
            ],
            'user_hist_applications' => [
                [
                    'name' => 'last',
                    'url' => 'http://localhost:1991/'
                ]
            ]
        ];

        $this->view->getRender(
            'identity',
            'profile',
            $data
        );

        $this->response->setContent($this->view->getContent());
        // $this->response->setStatusCode($response['status_code'] ?? 200);

        $this->response->send();

        exit;
    }
}
