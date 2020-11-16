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
use \Harpya\IP\Models\PasswordResetRequest;
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\IP\Models\User;

class ForgotPasswordController extends BaseController
{
    /**
     *
     */
    public function showForgotPasswordForm()
    {
        $this->setupCsrfToken();

        $this->view->getRender(
            'forgot_password',
            'forgotPassword',
            []
        );

        $this->response->setContent($this->view->getContent());
        $this->response->setStatusCode(200);

        $this->response->send();
    }

    protected function showResponseToRequestPasswordReset(string $msg, $isSuccess = true)
    {
        $this->view->msg = $msg;

        $this->view->getRender(
            'forgot_password',
            'forgotPasswordResponse',
            []
        );

        $this->response->setContent($this->view->getContent());

        if (!$isSuccess) {
            $this->response->setStatusCode(400);
        }

        $this->response->send();
    }

    public function processForgotPasswordRequest()
    {
        if (!$this->checkCsrfToken()) {
            $this->showResponseToRequestPasswordReset('Error on CSRF-Token', false);
            return;
        }
        $email = $this->request->getPost('email');

        $user = User::getByEmail($email);

        if ($user) {
            $requestResetPassword = PasswordResetRequest::buildFromUserID($user->id);
            $requestResetPassword->save();

            $resetPasswordURL = $this->config->application->hipBaseURL . '/password/forgot/';
            $resetPasswordURL .= $requestResetPassword->token;

            $this->view->siteName = $this->config->application->siteName;
            $this->view->siteURL = $this->config->application->hipBaseURL;
            $this->view->resetPasswordURL = $resetPasswordURL;
            $this->view->customerServiceEmail = $this->config->application->customerServiceEmail;

            $this->view->getRender(
                'forgot_password',
                'emailRequestResetPassword',
                []
            );

            $contents = $this->view->getContent();

            // send email
            if ($this->mailer) {
                $from = [];
                if ($this->config->communication->mail->fromEmail) {
                    if ($this->config->communication->mail->fromName) {
                        $from = [
                            $this->config->communication->mail->fromEmail => $this->config->communication->mail->fromName
                        ];
                    } else {
                        $from = [
                            $this->config->communication->mail->fromEmail
                        ];
                    }
                }

                // Create a message
                $message = (new \Swift_Message('Forgot password'))
                    ->setFrom($from)
                    ->setTo([$email])
                    ->setBody($contents, 'text/html');
                // Send the message
                $result = $this->mailer->send($message);

            // echo '<pre>';
                // print_r($result);
            } else {
                // echo 'Mail not configured';
            }

            // } else {
            // echo 'Email does not exists';
        }
        $this->showResponseToRequestPasswordReset('If you have a valid e-mail, you will receive a message with the link to reset your password');
    }

    public function showResetPasswordForm()
    {
    }

    public function proceedPasswordReset()
    {
    }
}
