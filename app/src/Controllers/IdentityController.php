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

class IdentityController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        // $this->view->setVar('my_var', 12345);
    }

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

    // public function signupAction(): void
    // {
    //     if ($this->request->isGet()) {
    //         $this->getSignup();
    //     } elseif ($this->request->isPost()) {
    //         $this->postSignup();
    //     } else {
    //         $this->dispatcher->forward([
    //             'controller' => 'errors',
    //             'action' => 'show401',
    //             'params' => [
    //                 'msg' => 'Invalid method'
    //             ]
    //         ]);
    //     }
    // }

    // protected function getSignup()
    // {
    //     $this->view->setVar('option', 'show_signup_form');
    // }

    // protected function postSignup()
    // {
    //     $this->view->setVar('option', 'show_signup_results');
    // }

    /**
     *
     */
    // public function signupAction222()
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
    //         // shows page
    //         // $this->dispatcher->forward([
    //         //     'controller' => 'index',
    //         //     'action' => 'index',
    //         // ]);
    //     }
    // }

    // public function profileAction()
    // {
    //     // show current logged user.
    //     $sessionData = $this->session->get('auth_data');
    //     print_r($sessionData);
    //     echo "\n\n ----- \n";
    //     print_r($_SESSION);
    //     // exit;
    //     return Constants::RESPONSE_PROCEED_VIEW_PROCESSING;
    // }
}
